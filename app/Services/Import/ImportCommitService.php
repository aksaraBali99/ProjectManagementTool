<?php

namespace App\Services\Import;

use App\Enums\DocumentAccessLevel;
use App\Enums\ImportBatchStatus;
use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\CompanyRoleSyncer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Writes a validated ImportBatch's import_rows to the real tables, one
 * stage per sheet-group, each in its own transaction, in the fixed order
 * the spec requires: Companies -> Departments -> Users -> Company Roles
 * (full resync) -> Projects -> Tasks+Subtasks+Task Documents+Task
 * Comments (the last three only depend on Tasks, so they share a stage).
 *
 * Every stage resolves cross-sheet references (Company name, Project
 * name, Task Ref, ...) against ImportCommitResolution — ids ACTUALLY
 * written moments earlier in this same commit — never against
 * import_rows.raw_data text. A stage failing after earlier stages
 * succeeded leaves the batch partially_committed with completed_stages
 * recorded, rather than the whole commit silently rolling everything
 * back to nothing.
 */
class ImportCommitService
{
    public function __construct(
        private readonly EmployeeIdGenerator $employeeIdGenerator,
        private readonly CompanyRoleSyncer $companyRoleSyncer,
    ) {}

    public function commit(ImportBatch $batch, User $importer): ImportCommitSummary
    {
        $batchUuid = (string) Str::uuid();
        app()->instance('import.current_batch_id', $batchUuid);

        $resolution = new ImportCommitResolution;
        $summary = new ImportCommitSummary($batch);
        $completedStages = [];

        try {
            $this->commitCompanies($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'companies';

            $this->commitDepartments($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'departments';

            $this->commitUsers($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'users';

            $this->commitCompanyRoles($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'company_roles';

            $this->commitProjects($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'projects';

            $this->commitTasksAndChildren($batch, $importer, $batchUuid, $resolution, $summary);
            $completedStages[] = 'tasks_and_children';

            $batch->update([
                'status' => ImportBatchStatus::Committed,
                'uuid' => $batchUuid,
                'completed_stages' => $completedStages,
                'committed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $batch->update([
                'status' => ImportBatchStatus::PartiallyCommitted,
                'uuid' => $batchUuid,
                'completed_stages' => $completedStages,
            ]);

            throw $e;
        } finally {
            app()->forgetInstance('import.current_batch_id');
        }

        return $summary;
    }

    // -- stages --------------------------------------------------

    private function commitCompanies(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $rows = $this->commitEligibleRows($batch, 'Companies');

            foreach ($rows as $row) {
                $data = $row->raw_data;
                $name = $data['name'];
                $idCode = $data['id'] ?? null;

                if ($idCode) {
                    $organizationId = ImportIdCodec::decode(ImportIdCodec::COMPANY_PREFIX, $idCode);
                    $organization = Organization::findOrFail($organizationId);
                    $before = $organization->name;
                    $organization->update(['name' => $name]);

                    if ($before !== $name) {
                        $this->writeAuditLog($importer, $batchUuid, $organization->id, 'organization.updated', 'organization', $organization->id, [
                            'name' => ['old' => $before, 'new' => $name],
                        ]);
                        $summary->increment('Companies', 'updated');
                    } else {
                        $summary->increment('Companies', 'unchanged');
                    }
                } else {
                    $organization = Organization::create([
                        'name' => $name,
                        'slug' => Organization::generateUniqueSlug($name),
                    ]);
                    $this->writeAuditLog($importer, $batchUuid, $organization->id, 'organization.created', 'organization', $organization->id, ['name' => $name]);
                    $summary->increment('Companies', 'created');
                }

                $resolution->organizationIdsByName[$resolution->normalize($name)] = $organization->id;
                $row->update(['entity_id' => $organization->id]);
            }
        });
    }

    private function commitDepartments(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $rows = $this->commitEligibleRows($batch, 'Departments');

            foreach ($rows as $row) {
                $data = $row->raw_data;
                $name = $data['name'];
                $companyName = $data['company'];
                $idCode = $data['id'] ?? null;
                $organizationId = $this->resolveOrganizationId($companyName, $resolution);

                if ($idCode) {
                    $departmentId = ImportIdCodec::decode(ImportIdCodec::DEPARTMENT_PREFIX, $idCode);
                    $department = Department::withoutGlobalScopes()->findOrFail($departmentId);
                    $before = ['name' => $department->name, 'organization_id' => $department->organization_id];
                    $department->update(['name' => $name, 'organization_id' => $organizationId]);

                    $diff = collect($before)
                        ->filter(fn ($old, $key) => $old !== $department->getAttribute($key))
                        ->mapWithKeys(fn ($old, $key) => [$key => ['old' => $old, 'new' => $department->getAttribute($key)]])
                        ->all();

                    if (! empty($diff)) {
                        $this->writeAuditLog($importer, $batchUuid, $organizationId, 'department.updated', 'department', $department->id, $diff);
                        $summary->increment('Departments', 'updated');
                    } else {
                        $summary->increment('Departments', 'unchanged');
                    }
                } else {
                    $department = Department::create(['organization_id' => $organizationId, 'name' => $name]);
                    $this->writeAuditLog($importer, $batchUuid, $organizationId, 'department.created', 'department', $department->id, ['name' => $name, 'organization_id' => $organizationId]);
                    $summary->increment('Departments', 'created');
                }

                $resolution->departmentIdsByKey[$resolution->departmentKey($companyName, $name)] = $department->id;
                $row->update(['entity_id' => $department->id]);
            }
        });
    }

    private function commitUsers(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $rows = $this->commitEligibleRows($batch, 'Users');

            // Users are global (not tenant-scoped), but audit_log.
            // organization_id is a required FK — use the first company
            // this username is mentioned for on the Company Roles tab in
            // this same file as the audit entry's scope.
            $firstCompanyByUsername = $this->commitEligibleRows($batch, 'Company Roles')
                ->groupBy(fn (ImportRow $row) => $row->raw_data['username'])
                ->map(fn ($rows) => $rows->first()->raw_data['company']);

            foreach ($rows as $row) {
                $data = $row->raw_data;
                $username = $data['username'];
                $existing = User::where('username', $username)->first() ?? User::where('email', $data['email'])->first();

                $type = ucfirst(strtolower($data['type']));
                $employeeId = $data['employee_id'] ?? null;
                if (empty($employeeId)) {
                    $employeeId = $this->employeeIdGenerator->next(strtolower($type));
                }

                $plainPassword = $data['password'] ?? null;
                if (empty($plainPassword) && ! $existing) {
                    $plainPassword = Str::password(16);
                    $summary->temporaryPasswords[$username] = $plainPassword;
                }

                $attributes = [
                    'username' => $username,
                    'name' => $data['name'],
                    'employee_id' => $employeeId,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ];

                if ($existing) {
                    $before = [
                        'username' => $existing->username,
                        'name' => $existing->name,
                        'employee_id' => $existing->employee_id,
                        'email' => $existing->email,
                        'phone' => $existing->phone,
                    ];
                    $existing->update($plainPassword ? [...$attributes, 'password' => $plainPassword] : $attributes);
                    $user = $existing;

                    $diff = collect($attributes)
                        ->filter(fn ($new, $key) => $before[$key] !== $new)
                        ->mapWithKeys(fn ($new, $key) => [$key => ['old' => $before[$key], 'new' => $new]])
                        ->all();
                    $action = 'user.updated';
                    $kind = 'updated';
                } else {
                    $user = User::create([...$attributes, 'password' => $plainPassword, 'is_active' => true]);
                    $diff = $attributes;
                    $action = 'user.created';
                    $kind = 'created';
                }

                $companyName = $firstCompanyByUsername[$username] ?? null;
                if ($companyName && (! empty($diff))) {
                    $organizationId = $this->resolveOrganizationId($companyName, $resolution);
                    $this->writeAuditLog($importer, $batchUuid, $organizationId, $action, 'user', $user->id, $diff);
                }

                $summary->increment('Users', empty($diff) ? 'unchanged' : $kind);
                $resolution->userIdsByUsername[$username] = $user->id;
                $row->update(['entity_id' => $user->id]);
            }
        });
    }

    private function commitCompanyRoles(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $rows = $this->commitEligibleRows($batch, 'Company Roles');
            $rowsByUsername = $rows->groupBy(fn (ImportRow $row) => $row->raw_data['username']);
            $roleSlugsByName = Role::assignableInCompany()->get()->keyBy(fn (Role $role) => strtolower($role->name));

            foreach ($rowsByUsername as $username => $userRows) {
                $userId = $this->resolveUserId($username, $resolution);
                $user = User::findOrFail($userId);

                $roles = [];
                foreach ($userRows as $row) {
                    $companyName = $row->raw_data['company'];
                    $roleSlug = $roleSlugsByName[strtolower(trim($row->raw_data['role']))]->slug;
                    $organizationId = $this->resolveOrganizationId($companyName, $resolution);
                    $roles[$organizationId] = $roleSlug;
                }

                $changes = $this->companyRoleSyncer->sync($user, $roles);

                foreach ($changes as $organizationId => $kind) {
                    $action = match ($kind) {
                        'created' => 'org_member.created',
                        'updated' => 'org_member.updated',
                        'deleted' => 'org_member.deleted',
                    };
                    $this->writeAuditLog($importer, $batchUuid, $organizationId, $action, 'org_member', $organizationId, [
                        'user_id' => $user->id,
                        'role' => $roles[$organizationId] ?? null,
                    ]);
                    $summary->increment('Company Roles', $kind);
                }

                if (empty($changes)) {
                    $summary->increment('Company Roles', 'unchanged');
                }

                foreach ($userRows as $row) {
                    $row->update(['entity_id' => $user->id]);
                }
            }
        });
    }

    private function commitProjects(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $rows = $this->commitEligibleRows($batch, 'Projects');

            foreach ($rows as $row) {
                $data = $row->raw_data;
                $name = $data['name'];
                $companyName = $data['company'];
                $idCode = $data['id'] ?? null;
                $organizationId = $this->resolveOrganizationId($companyName, $resolution);

                $clientUsername = $data['client_username'] ?? null;
                $clientUserId = $clientUsername ? $this->resolveUserId($clientUsername, $resolution) : null;

                $attributes = [
                    'organization_id' => $organizationId,
                    'name' => $name,
                    'description' => $data['description'],
                    'is_external' => (bool) $clientUserId,
                    'status' => $data['status'] ? $this->resolveEnumValue(ProjectStatus::cases(), $data['status']) : ProjectStatus::Open->value,
                    'priority' => $data['priority'] ? $this->resolveEnumValue(Priority::cases(), $data['priority']) : Priority::Medium->value,
                ];

                $before = null;

                if ($idCode) {
                    $projectId = ImportIdCodec::decode(ImportIdCodec::PROJECT_PREFIX, $idCode);
                    $project = Project::withoutGlobalScopes()->findOrFail($projectId);
                    $before = [
                        'organization_id' => $project->organization_id,
                        'name' => $project->name,
                        'description' => $project->description,
                        'is_external' => $project->is_external,
                        'status' => $project->status->value,
                        'priority' => $project->priority->value,
                    ];
                    $project->update($attributes);
                    $kind = 'updated';
                    $action = 'project.updated';
                } else {
                    $project = Project::create($attributes);
                    $kind = 'created';
                    $action = 'project.created';
                }

                $staffIds = collect(explode(',', $data['staff_usernames'] ?? ''))
                    ->map(fn ($u) => trim($u))
                    ->filter()
                    ->map(fn ($username) => $this->resolveUserId($username, $resolution))
                    ->filter()
                    ->all();

                $staffSync = $project->staff()->sync($staffIds);
                $clientSync = $project->clients()->sync($clientUserId ? [$clientUserId] : []);
                $pivotsChanged = ! empty($staffSync['attached']) || ! empty($staffSync['detached'])
                    || ! empty($clientSync['attached']) || ! empty($clientSync['detached']);

                $diff = $attributes;
                if ($before !== null) {
                    $diff = collect($attributes)
                        ->filter(fn ($new, $key) => $before[$key] !== $new)
                        ->mapWithKeys(fn ($new, $key) => [$key => ['old' => $before[$key], 'new' => $new]])
                        ->all();

                    if (empty($diff) && ! $pivotsChanged) {
                        $kind = 'unchanged';
                    }
                }

                if ($kind !== 'unchanged') {
                    $this->writeAuditLog($importer, $batchUuid, $organizationId, $action, 'project', $project->id, $diff);
                }
                $summary->increment('Projects', $kind);

                $resolution->projectIdsByKey[$resolution->projectKey($companyName, $name)] = $project->id;
                $row->update(['entity_id' => $project->id]);
            }
        });
    }

    private function commitTasksAndChildren(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        DB::transaction(function () use ($batch, $importer, $batchUuid, $resolution, $summary) {
            $this->commitTasks($batch, $resolution, $summary);
            $this->commitSubtasks($batch, $resolution, $summary);
            $this->commitTaskDocuments($batch, $importer, $batchUuid, $resolution, $summary);
            $this->commitTaskComments($batch, $importer, $resolution, $summary);
        });
    }

    private function commitTasks(ImportBatch $batch, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        $rows = $this->commitEligibleRows($batch, 'Tasks');

        foreach ($rows as $row) {
            $data = $row->raw_data;
            $taskRef = (int) $data['task_ref'];
            $companyName = $data['company'];
            $projectName = $data['project_name'];

            $organizationId = $this->resolveOrganizationId($companyName, $resolution);
            $projectId = $this->resolveProjectId($companyName, $projectName, $resolution);
            $departmentId = $this->resolveDepartmentId($companyName, $data['department'], $resolution);

            $assigneeUsername = $data['assignee_username'] ?? null;
            $assigneeId = $assigneeUsername ? $this->resolveUserId($assigneeUsername, $resolution) : null;

            $attributes = [
                'organization_id' => $organizationId,
                'project_id' => $projectId,
                'department_id' => $departmentId,
                'assignee_id' => $assigneeId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $this->resolveEnumValue(Priority::cases(), $data['priority']),
                'status' => $this->resolveEnumValue(TaskStatus::cases(), $data['status']),
                'due_date' => $this->parseDate($data['due_date'] ?? null),
                'start_date' => $this->parseDate($data['start_date'] ?? null),
            ];

            // Import's Assignee column accepts ANY company member
            // regardless of role — broader than the in-app form's
            // project-staff-only restriction — so a resolved assignee not
            // already on the project is auto-attached, otherwise the
            // task would carry an assignee invisible to every other
            // staff-scoped UI surface (Kanban filters, etc.).
            if ($assigneeId) {
                $project = Project::withoutGlobalScopes()->find($projectId);
                $alreadyOnProject = $project->staff()->where('users.id', $assigneeId)->exists()
                    || $project->clients()->where('users.id', $assigneeId)->exists();
                if (! $alreadyOnProject) {
                    $project->staff()->syncWithoutDetaching([$assigneeId]);
                }
            }

            $existingTask = Task::withoutGlobalScopes()->where('project_id', $projectId)->where('title', $data['title'])->first();

            if ($existingTask) {
                $existingTask->update($attributes);
                $task = $existingTask;
                $summary->increment('Tasks', 'updated');
            } else {
                $task = Task::create($attributes);
                $summary->increment('Tasks', 'created');
            }

            $resolution->taskIdsByRef[$taskRef] = $task->id;
            $row->update(['entity_id' => $task->id]);
        }
    }

    private function commitSubtasks(ImportBatch $batch, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        $rows = $this->commitEligibleRows($batch, 'Subtasks');

        foreach ($rows as $row) {
            $data = $row->raw_data;
            $task = Task::withoutGlobalScopes()->find($resolution->taskIdsByRef[(int) $data['task_ref']]);

            $subtask = $task->subtasks()->create([
                'title' => $data['title'],
                'assignee_id' => ! empty($data['assignee_username'])
                    ? $this->resolveUserId($data['assignee_username'], $resolution)
                    : $task->assignee_id,
                'due_date' => $this->parseDate($data['due_date'] ?? null) ?? $task->due_date,
                'start_date' => $this->parseDate($data['start_date'] ?? null) ?? $task->start_date,
            ]);

            $summary->increment('Subtasks', 'created');
            $row->update(['entity_id' => $subtask->id]);
        }
    }

    private function commitTaskDocuments(ImportBatch $batch, User $importer, string $batchUuid, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        $rows = $this->commitEligibleRows($batch, 'Task Documents');

        foreach ($rows as $row) {
            $data = $row->raw_data;
            $task = Task::withoutGlobalScopes()->find($resolution->taskIdsByRef[(int) $data['task_ref']]);

            $document = Document::create([
                'organization_id' => $task->organization_id,
                'uploaded_by' => $importer->id,
                'name' => $data['name'],
                'link' => $data['link'],
                'access_level' => DocumentAccessLevel::Internal,
            ]);
            $task->documents()->syncWithoutDetaching([$document->id]);

            $this->writeAuditLog($importer, $batchUuid, $task->organization_id, 'document.created', 'document', $document->id, [
                'name' => $data['name'],
                'link' => $data['link'],
                'access_level' => DocumentAccessLevel::Internal->value,
            ]);

            $summary->increment('Task Documents', 'created');
            $row->update(['entity_id' => $document->id]);
        }
    }

    private function commitTaskComments(ImportBatch $batch, User $importer, ImportCommitResolution $resolution, ImportCommitSummary $summary): void
    {
        $rows = $this->commitEligibleRows($batch, 'Task Comments');

        foreach ($rows as $row) {
            $data = $row->raw_data;
            $task = Task::withoutGlobalScopes()->find($resolution->taskIdsByRef[(int) $data['task_ref']]);

            $comment = $task->comments()->create([
                'user_id' => $importer->id,
                'body' => $data['body'],
            ]);

            $summary->increment('Task Comments', 'created');
            $row->update(['entity_id' => $comment->id]);
        }
    }

    // -- shared helpers --------------------------------------------------

    /**
     * @return Collection<int, ImportRow>
     */
    private function commitEligibleRows(ImportBatch $batch, string $sheetName): Collection
    {
        return $batch->importRows()
            ->where('sheet_name', $sheetName)
            ->where('validation_status', '!=', 'error')
            // A Subtask/Task Document/Task Comment can be 'blocked' with a
            // 'valid' status — its own fields are fine, but its parent Task
            // failed, so it still can't commit (there's no task to attach
            // it to). Filtering on status alone would let it through.
            ->where('resolved_action', '!=', 'blocked')
            ->orderBy('row_number')
            ->get();
    }

    private function resolveOrganizationId(string $companyName, ImportCommitResolution $resolution): int
    {
        $key = $resolution->normalize($companyName);

        return $this->resolveId(
            $resolution->organizationIdsByName[$key] ?? null,
            "org:{$key}",
            $resolution,
            fn () => Organization::where('name', $companyName)->value('id'),
        );
    }

    private function resolveDepartmentId(string $companyName, string $departmentName, ImportCommitResolution $resolution): int
    {
        $key = $resolution->departmentKey($companyName, $departmentName);

        return $this->resolveId(
            $resolution->departmentIdsByKey[$key] ?? null,
            "dept:{$key}",
            $resolution,
            fn () => Department::withoutGlobalScopes()
                ->whereHas('organization', fn ($query) => $query->where('name', $companyName))
                ->where('name', $departmentName)
                ->value('id'),
        );
    }

    private function resolveProjectId(string $companyName, string $projectName, ImportCommitResolution $resolution): int
    {
        $key = $resolution->projectKey($companyName, $projectName);

        return $this->resolveId(
            $resolution->projectIdsByKey[$key] ?? null,
            "project:{$key}",
            $resolution,
            fn () => Project::withoutGlobalScopes()
                ->whereHas('organization', fn ($query) => $query->where('name', $companyName))
                ->where('name', $projectName)
                ->value('id'),
        );
    }

    private function resolveUserId(string $username, ImportCommitResolution $resolution): ?int
    {
        $key = $resolution->normalize($username);

        return $this->resolveId(
            $resolution->userIdsByUsername[$username] ?? null,
            "user:{$key}",
            $resolution,
            fn () => User::where('username', $username)->value('id'),
        );
    }

    /**
     * Shared by all four resolve*Id() methods above: return the id already
     * known from this commit (something written moments earlier in the
     * SAME commit), or fall back to a DB lookup — memoized per cache key
     * so a name referenced many times across a large file only triggers
     * one fallback query.
     */
    private function resolveId(?int $mapped, string $cacheKey, ImportCommitResolution $resolution, \Closure $fallbackQuery): ?int
    {
        if ($mapped !== null) {
            return $mapped;
        }

        if (! array_key_exists($cacheKey, $resolution->fallbackIdCache)) {
            $resolution->fallbackIdCache[$cacheKey] = $fallbackQuery();
        }

        return $resolution->fallbackIdCache[$cacheKey];
    }

    /**
     * @param  array<int, \BackedEnum&object{label(): string}>  $cases
     */
    private function resolveEnumValue(array $cases, string $label): string
    {
        return ImportFieldResolver::resolveEnumValue($cases, $label);
    }

    private function parseDate(?string $value): ?string
    {
        return ImportFieldResolver::parseDate($value);
    }

    private function writeAuditLog(User $importer, string $batchUuid, int $organizationId, string $action, string $entityType, int $entityId, array $changes): void
    {
        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => $importer->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'import_batch_id' => $batchUuid,
            'changes' => array_merge($changes, ['source' => 'import']),
        ]);
    }
}
