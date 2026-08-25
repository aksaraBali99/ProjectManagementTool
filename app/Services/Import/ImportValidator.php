<?php

namespace App\Services\Import;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Support\CompanyRoleRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rules\Password;

/**
 * Runs validation EXACTLY ONCE, at upload time, writing one ImportRow per
 * sheet row into import_rows — the review grid (Part 4) and commit
 * (Part 5) both read from there, never re-parsing or re-validating.
 *
 * Sheets are processed in strict dependency order (Companies ->
 * Departments -> Users -> Company Roles -> cross-validate -> Projects ->
 * Tasks -> Subtasks/Task Documents/Task Comments), threading one
 * ImportValidationContext through the whole pass so a later sheet can
 * check whether something an earlier sheet referenced was valid, and a
 * failed row automatically blocks anything that depended on it
 * (cascading invalidation) instead of failing independently.
 */
class ImportValidator
{
    public const MAX_TOTAL_ROWS = 5000;

    public function __construct(
        private readonly ImportSpreadsheetParser $parser,
        private readonly EmployeeIdGenerator $employeeIdGenerator,
        private readonly DuplicateDetector $duplicateDetector,
    ) {}

    /**
     * @return array<string, int> total row count per sheet — used by the
     *                            controller to enforce MAX_TOTAL_ROWS
     *                            before a batch is even created.
     */
    public function countRows(string $filePath): array
    {
        $sheets = $this->parser->parse($filePath);

        return collect($sheets)->map(fn ($rows) => count($rows))->all();
    }

    public function validate(ImportBatch $batch, string $filePath): void
    {
        $sheets = $this->parser->parse($filePath);
        $context = new ImportValidationContext;

        DB::transaction(function () use ($batch, $sheets, $context) {
            $this->insertRows($batch, 'Companies', $this->validateCompanies($sheets['Companies'], $context), $context);
            $this->insertRows($batch, 'Departments', $this->validateDepartments($sheets['Departments'], $context), $context);
            $this->insertRows($batch, 'Users', $this->validateUsers($sheets['Users'], $context), $context);
            $this->insertRows($batch, 'Company Roles', $this->validateCompanyRoles($sheets['Company Roles'], $context), $context);
            $this->crossValidateUserTypeAgainstRoles($context);
            $this->insertRows($batch, 'Projects', $this->validateProjects($sheets['Projects'], $context), $context);
            $this->insertRows($batch, 'Tasks', $this->validateTasks($sheets['Tasks'], $context), $context);
            $this->insertRows($batch, 'Subtasks', $this->validateSubtasks($sheets['Subtasks'], $context), $context);
            $this->insertRows($batch, 'Task Documents', $this->validateTaskDocuments($sheets['Task Documents'], $context), $context);
            $this->insertRows($batch, 'Task Comments', $this->validateTaskComments($sheets['Task Comments'], $context), $context);
        });
    }

    // -- Companies --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateCompanies(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $name = $cells['name'];
            $idCode = $cells['id'];

            if (empty($name)) {
                $result[] = $this->errorRow('Companies', $row, 'Company Name is required.');

                continue;
            }

            if ($idCode === null) {
                $context->validCompanyNames[$context->companyKey($name)] = true;
                $result[] = $this->row('Companies', $row, 'insert', 'valid');

                continue;
            }

            $organizationId = ImportIdCodec::decode(ImportIdCodec::COMPANY_PREFIX, $idCode);
            if ($organizationId === null || ! Organization::whereKey($organizationId)->exists()) {
                $context->blockedCompanyNames[$context->companyKey($name)] = true;
                $result[] = $this->errorRow('Companies', $row, "ID \"{$idCode}\" does not match any existing company.");

                continue;
            }

            $context->validCompanyNames[$context->companyKey($name)] = true;
            $result[] = $this->row('Companies', $row, 'update', 'valid');
        }

        return $result;
    }

    // -- Departments --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateDepartments(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $name = $cells['name'];
            $companyName = $cells['company'];
            $idCode = $cells['id'];

            if (empty($name)) {
                $result[] = $this->errorRow('Departments', $row, 'Department Name is required.');

                continue;
            }

            if (empty($companyName)) {
                $result[] = $this->errorRow('Departments', $row, 'Company is required.');

                continue;
            }

            $companyState = $this->companyState($companyName, $context);
            if ($companyState === 'blocked') {
                $result[] = $this->blockedRow('Departments', $row, "Blocked: referenced Company \"{$companyName}\" failed validation.");

                continue;
            }

            if ($companyState === 'missing') {
                $result[] = $this->errorRow('Departments', $row, "Company \"{$companyName}\" does not exist.");

                continue;
            }

            $deptKey = $context->departmentKey($companyName, $name);

            if ($idCode === null) {
                if (isset($context->validDepartmentKeys[$deptKey])) {
                    $result[] = $this->errorRow('Departments', $row, "Duplicate: \"{$name}\" is already being added for \"{$companyName}\" earlier in this file.");

                    continue;
                }

                $context->validDepartmentKeys[$deptKey] = true;
                $result[] = $this->row('Departments', $row, 'insert', 'valid');

                continue;
            }

            $departmentId = ImportIdCodec::decode(ImportIdCodec::DEPARTMENT_PREFIX, $idCode);
            if ($departmentId === null || ! Department::withoutGlobalScopes()->whereKey($departmentId)->exists()) {
                $context->blockedDepartmentKeys[$deptKey] = true;
                $result[] = $this->errorRow('Departments', $row, "ID \"{$idCode}\" does not match any existing department.");

                continue;
            }

            $context->validDepartmentKeys[$deptKey] = true;
            $result[] = $this->row('Departments', $row, 'update', 'valid');
        }

        return $result;
    }

    // -- Users --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateUsers(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $username = $cells['username'];
            $name = $cells['name'];
            $type = $cells['type'];
            $email = $cells['email'];
            $phone = $cells['phone'];

            $missing = collect(['username' => $username, 'name' => $name, 'type' => $type, 'email' => $email, 'phone' => $phone])
                ->filter(fn ($value) => empty($value))
                ->keys();

            if ($missing->isNotEmpty()) {
                $result[] = $this->errorRow('Users', $row, 'Missing required field(s): '.$missing->implode(', ').'.');

                continue;
            }

            $normalizedType = ucfirst(strtolower(trim($type)));
            if (! in_array($normalizedType, ['Employee', 'Client'], true)) {
                $result[] = $this->errorRow('Users', $row, 'Type must be Employee or Client.');

                continue;
            }

            $existingByUsername = User::where('username', $username)->first();
            $existingByEmail = User::where('email', $email)->first();

            if ($existingByUsername && $existingByEmail && $existingByUsername->id !== $existingByEmail->id) {
                $result[] = $this->errorRow('Users', $row, "Ambiguous: Username \"{$username}\" and Email \"{$email}\" match two different existing users.");

                continue;
            }

            $existing = $existingByUsername ?? $existingByEmail;

            $emailKey = $context->normalize($email);
            if (isset($context->emailsSeenThisFile[$emailKey]) && $context->emailsSeenThisFile[$emailKey] !== $username) {
                $result[] = $this->errorRow('Users', $row, "Duplicate: Email \"{$email}\" is already used by another row in this file.");

                continue;
            }

            $phoneError = null;
            (new ValidPhoneNumber)->validate('phone', $phone, function (string $message) use (&$phoneError) {
                $phoneError = $message;
            });
            if ($phoneError !== null) {
                $result[] = $this->errorRow('Users', $row, $phoneError);

                continue;
            }

            $password = $cells['password'];
            if (! empty($password)) {
                $passwordValidator = ValidatorFacade::make(['password' => $password], [
                    'password' => ['string', Password::min(8)->mixedCase()->numbers()->symbols()],
                ]);
                if ($passwordValidator->fails()) {
                    $result[] = $this->errorRow('Users', $row, $passwordValidator->errors()->first('password'));

                    continue;
                }
            }

            $employeeId = $cells['employee_id'];
            if (! empty($employeeId)) {
                $employeeIdTaken = User::where('employee_id', $employeeId)
                    ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
                    ->exists();
                if ($employeeIdTaken) {
                    $result[] = $this->errorRow('Users', $row, "Employee ID \"{$employeeId}\" is already in use.");

                    continue;
                }
            } elseif (! $existing) {
                // Preview-only, written into raw_data for the review
                // grid's benefit — discarded at commit time in favor of a
                // freshly-generated id (time may have passed and other
                // users may have been created between upload and commit).
                $prefix = $this->employeeIdGenerator->prefixFor(strtolower($normalizedType));
                $previewedEmployeeId = $this->employeeIdGenerator->next(strtolower($normalizedType), $context->employeeIdClaimed);
                $context->employeeIdClaimed[$prefix] = ImportIdCodec::decode($prefix, $previewedEmployeeId);
                $row['cells']['employee_id'] = $previewedEmployeeId;
            }

            $context->emailsSeenThisFile[$emailKey] = $username;

            $duplicateWarning = null;
            if (! $existing) {
                $candidates = User::query()
                    ->where(function ($query) use ($username, $email) {
                        $query->where('username', '!=', $username)->where('email', '!=', $email);
                    })
                    ->limit(200)
                    ->get(['name', 'email'])
                    ->map(fn (User $user) => ['name' => $user->name, 'email' => $user->email])
                    ->all();

                $duplicateWarning = $this->duplicateDetector->findSimilarUser($name, $email, $candidates);
            }

            $action = $existing ? 'update' : 'insert';
            $status = $duplicateWarning ? 'warning' : 'valid';

            $entry = $this->row('Users', $row, $action, $status, $duplicateWarning);
            $entry['__username'] = $username;
            $result[] = $entry;

            $context->usersSeen[$username] = [
                'type' => $normalizedType,
                'valid' => true,
                // filled in by insertRows(), once the row actually exists
                'importRowId' => null,
            ];
        }

        return $result;
    }

    // -- Company Roles --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateCompanyRoles(array $rows, ImportValidationContext $context): array
    {
        $result = [];
        $assignableSlugsByName = Role::assignableInCompany()->get()->keyBy(fn (Role $role) => strtolower($role->name));

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $username = $cells['username'];
            $companyName = $cells['company'];
            $roleName = $cells['role'];

            if (empty($username) || empty($companyName) || empty($roleName)) {
                $result[] = $this->errorRow('Company Roles', $row, 'Username, Company, and Role are all required.');

                continue;
            }

            $userInfo = $this->resolveUsername($username, $context);
            if (! $userInfo['exists']) {
                $result[] = $this->errorRow('Company Roles', $row, "Username \"{$username}\" does not exist and isn't on the Users tab in this file.");

                continue;
            }
            if (! $userInfo['valid']) {
                $result[] = $this->blockedRow('Company Roles', $row, "Blocked: Username \"{$username}\" failed validation on the Users tab.");

                continue;
            }

            $companyKey = $context->companyKey($companyName);
            $companyState = $this->companyState($companyName, $context);
            if ($companyState === 'blocked') {
                $result[] = $this->blockedRow('Company Roles', $row, "Blocked: referenced Company \"{$companyName}\" failed validation.");

                continue;
            }
            if ($companyState === 'missing') {
                $result[] = $this->errorRow('Company Roles', $row, "Company \"{$companyName}\" does not exist.");

                continue;
            }

            $role = $assignableSlugsByName->get(strtolower(trim($roleName)));
            if (! $role) {
                $result[] = $this->errorRow('Company Roles', $row, 'Role must be one of: '.$assignableSlugsByName->keys()->map(fn ($n) => ucfirst($n))->implode(', ').'.');

                continue;
            }

            $fileKey = $context->companyRoleFileKey($username, $companyName);
            if (isset($context->companyRoleFileKeys[$fileKey])) {
                $result[] = $this->errorRow('Company Roles', $row, "Duplicate: \"{$username}\" already has a row for \"{$companyName}\" earlier in this file.");

                continue;
            }
            $context->companyRoleFileKeys[$fileKey] = true;

            $context->companyRolesByUsername[$username] ??= $this->existingRolesFor($username, $context);
            $context->companyRolesByUsername[$username][$companyKey] = $role->slug;

            $result[] = $this->row('Company Roles', $row, 'sync', 'valid');
        }

        return $result;
    }

    /**
     * Seeds a username's role map with whatever they already hold in the
     * database (for an existing user), keyed by normalized company name —
     * so the Client-exclusivity check below sees the FULL post-import
     * picture (existing untouched roles + this file's rows), not just the
     * rows this file happens to mention for them.
     *
     * @return array<string, string> normalized company name => role slug
     */
    private function existingRolesFor(string $username, ImportValidationContext $context): array
    {
        if (isset($context->existingRolesCache[$username])) {
            return $context->existingRolesCache[$username];
        }

        $user = User::where('username', $username)->first();
        if (! $user) {
            return $context->existingRolesCache[$username] = [];
        }

        return $context->existingRolesCache[$username] = OrgMember::where('user_id', $user->id)
            ->with(['organization:id,name', 'role:id,slug'])
            ->get()
            ->filter(fn (OrgMember $member) => $member->organization && $member->role)
            ->mapWithKeys(fn (OrgMember $member) => [$context->companyKey($member->organization->name) => $member->role->slug])
            ->all();
    }

    /**
     * Runs after both Users and Company Roles are parsed: every
     * file-provided user needs >=1 Company Roles row, and their Type must
     * be internally consistent with the FULL merged role set built above
     * (Client Type <-> only Role=Client; Employee Type <-> only
     * Staff/Management) — reusing the exact same rule CompanyRoleRules
     * already enforces on the in-app User form.
     */
    private function crossValidateUserTypeAgainstRoles(ImportValidationContext $context): void
    {
        foreach ($context->usersSeen as $username => $info) {
            if (! $info['valid'] || $info['importRowId'] === null) {
                continue;
            }

            $roles = $context->companyRolesByUsername[$username] ?? [];

            if (empty($roles)) {
                $this->markUserRowInvalid($info['importRowId'], $context, $username, 'This user has no Company Roles row — every user needs at least one.');

                continue;
            }

            $ruleError = CompanyRoleRules::validateRoleCombination($roles, grantingSuperAdmin: false);
            if ($ruleError !== null) {
                $this->markUserRowInvalid($info['importRowId'], $context, $username, $ruleError);

                continue;
            }

            $hasClientRole = in_array(Role::CLIENT, $roles, true);
            $expectedType = $hasClientRole ? 'Client' : 'Employee';
            if ($info['type'] !== $expectedType) {
                $this->markUserRowInvalid(
                    $info['importRowId'],
                    $context,
                    $username,
                    "Type is \"{$info['type']}\" but the Company Roles tab assigns them a {$expectedType}-only role set."
                );
            }
        }
    }

    private function markUserRowInvalid(int $importRowId, ImportValidationContext $context, string $username, string $message): void
    {
        ImportRow::whereKey($importRowId)->update([
            'resolved_action' => 'blocked',
            'validation_status' => 'error',
            'validation_message' => $message,
        ]);

        $context->usersSeen[$username]['valid'] = false;
    }

    // -- Projects --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateProjects(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $name = $cells['name'];
            $description = $cells['description'];
            $companyName = $cells['company'];
            $idCode = $cells['id'];

            if (empty($name) || empty($description)) {
                $result[] = $this->errorRow('Projects', $row, 'Project Name and Description are required.');

                continue;
            }

            if (empty($companyName)) {
                $result[] = $this->errorRow('Projects', $row, 'Company is required.');

                continue;
            }

            $companyKey = $context->companyKey($companyName);
            $companyState = $this->companyState($companyName, $context);
            if ($companyState === 'blocked') {
                $result[] = $this->blockedRow('Projects', $row, "Blocked: referenced Company \"{$companyName}\" failed validation.");

                continue;
            }
            if ($companyState === 'missing') {
                $result[] = $this->errorRow('Projects', $row, "Company \"{$companyName}\" does not exist.");

                continue;
            }

            if ($cells['status'] && ! $this->matchesLabel($cells['status'], ProjectStatus::cases())) {
                $result[] = $this->errorRow('Projects', $row, 'Status must be one of: '.collect(ProjectStatus::cases())->map->label()->implode(', ').'.');

                continue;
            }

            if ($cells['priority'] && ! $this->matchesLabel($cells['priority'], Priority::cases())) {
                $result[] = $this->errorRow('Projects', $row, 'Priority must be one of: '.collect(Priority::cases())->map->label()->implode(', ').'.');

                continue;
            }

            $clientUsername = $cells['client_username'];
            if (! empty($clientUsername)) {
                $userInfo = $this->resolveUsername($clientUsername, $context);
                if (! $userInfo['exists'] || ! $userInfo['valid']) {
                    $result[] = $this->errorRow('Projects', $row, "Client Username \"{$clientUsername}\" is not a valid user in this file or the database.");

                    continue;
                }

                $clientRole = $context->companyRolesByUsername[$clientUsername][$companyKey]
                    ?? $this->existingRolesFor($clientUsername, $context)[$companyKey]
                    ?? null;

                if ($clientRole !== Role::CLIENT) {
                    $result[] = $this->errorRow('Projects', $row, "Client Username \"{$clientUsername}\" does not hold the Client role for \"{$companyName}\".");

                    continue;
                }
            }

            $staffError = null;
            $staffUsernames = collect(explode(',', $cells['staff_usernames'] ?? ''))
                ->map(fn ($u) => trim($u))
                ->filter();

            foreach ($staffUsernames as $staffUsername) {
                $userInfo = $this->resolveUsername($staffUsername, $context);
                if (! $userInfo['exists'] || ! $userInfo['valid']) {
                    $staffError = "Assigned Staff \"{$staffUsername}\" is not a valid user in this file or the database.";
                    break;
                }

                $belongsToCompany = array_key_exists($companyKey, $context->companyRolesByUsername[$staffUsername] ?? $this->existingRolesFor($staffUsername, $context));
                if (! $belongsToCompany) {
                    $staffError = "Assigned Staff \"{$staffUsername}\" does not belong to \"{$companyName}\".";
                    break;
                }
            }

            if ($staffError !== null) {
                $result[] = $this->errorRow('Projects', $row, $staffError);

                continue;
            }

            $projectKey = $context->projectKey($companyName, $name);

            if ($idCode === null) {
                $context->validProjectKeys[$projectKey] = true;
                $result[] = $this->row('Projects', $row, 'insert', 'valid');

                continue;
            }

            $projectId = ImportIdCodec::decode(ImportIdCodec::PROJECT_PREFIX, $idCode);
            if ($projectId === null || ! Project::withoutGlobalScopes()->whereKey($projectId)->exists()) {
                $context->blockedProjectKeys[$projectKey] = true;
                $result[] = $this->errorRow('Projects', $row, "ID \"{$idCode}\" does not match any existing project.");

                continue;
            }

            $context->validProjectKeys[$projectKey] = true;
            $result[] = $this->row('Projects', $row, 'update', 'valid');
        }

        return $result;
    }

    // -- Tasks --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateTasks(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];
            $taskRef = $cells['task_ref'];
            $projectName = $cells['project_name'];
            $companyName = $cells['company'];
            $departmentName = $cells['department'];
            $title = $cells['title'];

            if (empty($taskRef) || ! ctype_digit((string) $taskRef)) {
                $result[] = $this->errorRow('Tasks', $row, 'Task Ref is required and must be a plain number.');

                continue;
            }
            $taskRefNumber = (int) $taskRef;

            if (isset($context->taskRefs[$taskRefNumber])) {
                $result[] = $this->errorRow('Tasks', $row, "Duplicate: Task Ref {$taskRefNumber} is already used earlier in this file.");

                continue;
            }

            if (empty($title) || empty($projectName) || empty($companyName) || empty($departmentName)) {
                $result[] = $this->errorRow('Tasks', $row, 'Title, Project Name, Company, and Department are all required.');
                $context->taskRefs[$taskRefNumber] = ['title' => (string) $title, 'companyName' => (string) $companyName, 'projectName' => (string) $projectName, 'blocked' => true];

                continue;
            }

            $companyKey = $context->companyKey($companyName);
            $companyState = $this->companyState($companyName, $context);
            if ($companyState === 'blocked') {
                $result[] = $this->blockedRow('Tasks', $row, "Blocked: referenced Company \"{$companyName}\" failed validation.");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }
            if ($companyState === 'missing') {
                $result[] = $this->errorRow('Tasks', $row, "Company \"{$companyName}\" does not exist.");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }

            $deptState = $this->departmentState($companyName, $departmentName, $context);
            if ($deptState === 'blocked') {
                $result[] = $this->blockedRow('Tasks', $row, "Blocked: referenced Department \"{$departmentName}\" failed validation.");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }
            if ($deptState === 'missing') {
                $result[] = $this->errorRow('Tasks', $row, "Department \"{$departmentName}\" does not belong to \"{$companyName}\".");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }

            $projectState = $this->projectState($companyName, $projectName, $context);
            if ($projectState === 'blocked') {
                $result[] = $this->blockedRow('Tasks', $row, "Blocked: referenced Project \"{$projectName}\" failed validation.");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }
            if ($projectState === 'missing') {
                $result[] = $this->errorRow('Tasks', $row, "Project \"{$projectName}\" does not exist for \"{$companyName}\".");
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }

            if (! $this->matchesLabel($cells['priority'] ?? '', Priority::cases())) {
                $result[] = $this->errorRow('Tasks', $row, 'Priority must be one of: '.collect(Priority::cases())->map->label()->implode(', ').'.');
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }

            if (! $this->matchesLabel($cells['status'] ?? '', TaskStatus::cases())) {
                $result[] = $this->errorRow('Tasks', $row, 'Status must be one of: '.collect(TaskStatus::cases())->map->label()->implode(', ').'.');
                $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                continue;
            }

            $assigneeUsername = $cells['assignee_username'];
            if (! empty($assigneeUsername)) {
                $userInfo = $this->resolveUsername($assigneeUsername, $context);
                if (! $userInfo['exists'] || ! $userInfo['valid']) {
                    $result[] = $this->errorRow('Tasks', $row, "Assignee \"{$assigneeUsername}\" is not a valid user in this file or the database.");
                    $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                    continue;
                }

                $belongsToCompany = array_key_exists($companyKey, $context->companyRolesByUsername[$assigneeUsername] ?? $this->existingRolesFor($assigneeUsername, $context));
                if (! $belongsToCompany) {
                    $result[] = $this->errorRow('Tasks', $row, "Assignee \"{$assigneeUsername}\" does not belong to \"{$companyName}\".");
                    $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => true];

                    continue;
                }
            }

            $existingTask = Task::withoutGlobalScopes()->whereHas('project', function ($query) use ($projectName) {
                $query->where('name', $projectName);
            })->where('title', $title)->first();

            $duplicateWarning = null;
            $action = 'insert';

            if ($existingTask) {
                $action = 'update';
            } else {
                $existingTitles = Task::withoutGlobalScopes()
                    ->whereHas('project', fn ($query) => $query->where('name', $projectName))
                    ->pluck('title')
                    ->all();
                $duplicateWarning = $this->duplicateDetector->findSimilarTaskTitle($title, $existingTitles);
            }

            $context->taskRefs[$taskRefNumber] = ['title' => $title, 'companyName' => $companyName, 'projectName' => $projectName, 'blocked' => false];

            $status = $duplicateWarning ? 'warning' : 'valid';
            $result[] = $this->row('Tasks', $row, $action, $status, $duplicateWarning);
        }

        return $result;
    }

    // -- Subtasks / Task Documents / Task Comments --------------------------------------------------------

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateSubtasks(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];

            if (empty($cells['title'])) {
                $result[] = $this->errorRow('Subtasks', $row, 'Subtask Title is required.');

                continue;
            }

            $taskRefError = $this->checkTaskRef($cells['task_ref'] ?? null, $context, 'Subtasks', $row);
            if ($taskRefError !== null) {
                $result[] = $taskRefError;

                continue;
            }

            $result[] = $this->row('Subtasks', $row, 'insert', 'valid');
        }

        return $result;
    }

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateTaskDocuments(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];

            if (empty($cells['name']) || empty($cells['link'])) {
                $result[] = $this->errorRow('Task Documents', $row, 'Document Name and Document Link are required.');

                continue;
            }

            $taskRefError = $this->checkTaskRef($cells['task_ref'] ?? null, $context, 'Task Documents', $row);
            if ($taskRefError !== null) {
                $result[] = $taskRefError;

                continue;
            }

            $result[] = $this->row('Task Documents', $row, 'insert', 'valid');
        }

        return $result;
    }

    /**
     * @param  array<int, array{row_number: int, cells: array<string, ?string>}>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateTaskComments(array $rows, ImportValidationContext $context): array
    {
        $result = [];

        foreach ($rows as $row) {
            $cells = $row['cells'];

            if (empty($cells['body'])) {
                $result[] = $this->errorRow('Task Comments', $row, 'Comment Body is required.');

                continue;
            }

            if (mb_strlen($cells['body']) > 2000) {
                $result[] = $this->errorRow('Task Comments', $row, 'Comment Body must be 2000 characters or fewer.');

                continue;
            }

            $taskRefError = $this->checkTaskRef($cells['task_ref'] ?? null, $context, 'Task Comments', $row);
            if ($taskRefError !== null) {
                $result[] = $taskRefError;

                continue;
            }

            $result[] = $this->row('Task Comments', $row, 'insert', 'valid');
        }

        return $result;
    }

    /**
     * @param  array{row_number: int, cells: array<string, ?string>}  $row
     * @return array<string, mixed>|null null when the Task Ref resolves cleanly
     */
    private function checkTaskRef(?string $taskRef, ImportValidationContext $context, string $sheetName, array $row): ?array
    {
        if (empty($taskRef) || ! ctype_digit($taskRef)) {
            return $this->errorRow($sheetName, $row, 'Task Ref is required and must match a number used on the Tasks tab.');
        }

        $taskRefNumber = (int) $taskRef;
        $taskInfo = $context->taskRefs[$taskRefNumber] ?? null;

        if ($taskInfo === null) {
            return $this->errorRow($sheetName, $row, "Task Ref {$taskRefNumber} does not match any row on the Tasks tab in this file.");
        }

        if ($taskInfo['blocked']) {
            return $this->blockedRow($sheetName, $row, "Blocked: Task Ref {$taskRefNumber} failed validation on the Tasks tab.");
        }

        return null;
    }

    // -- shared helpers --------------------------------------------------

    /**
     * A Company/Department/Project name doesn't need to appear on this
     * file's own Companies/Departments/Projects tab to be a valid
     * reference elsewhere — most imports only list NEW or CHANGED rows,
     * so an existing record simply not mentioned in this file is still
     * perfectly valid. Checks file state first (blocked/valid), then
     * falls back to the database for anything the file didn't touch.
     *
     * @return 'blocked'|'valid'|'missing'
     */
    private function companyState(string $companyName, ImportValidationContext $context): string
    {
        $key = $context->companyKey($companyName);

        if (isset($context->blockedCompanyNames[$key])) {
            return 'blocked';
        }

        if (isset($context->validCompanyNames[$key]) || Organization::where('name', $companyName)->exists()) {
            return 'valid';
        }

        return 'missing';
    }

    /**
     * @return 'blocked'|'valid'|'missing'
     */
    private function departmentState(string $companyName, string $departmentName, ImportValidationContext $context): string
    {
        $key = $context->departmentKey($companyName, $departmentName);

        if (isset($context->blockedDepartmentKeys[$key])) {
            return 'blocked';
        }

        if (isset($context->validDepartmentKeys[$key])) {
            return 'valid';
        }

        $exists = Department::withoutGlobalScopes()
            ->whereHas('organization', fn ($query) => $query->where('name', $companyName))
            ->where('name', $departmentName)
            ->exists();

        return $exists ? 'valid' : 'missing';
    }

    /**
     * @return 'blocked'|'valid'|'missing'
     */
    private function projectState(string $companyName, string $projectName, ImportValidationContext $context): string
    {
        $key = $context->projectKey($companyName, $projectName);

        if (isset($context->blockedProjectKeys[$key])) {
            return 'blocked';
        }

        if (isset($context->validProjectKeys[$key])) {
            return 'valid';
        }

        $exists = Project::withoutGlobalScopes()
            ->whereHas('organization', fn ($query) => $query->where('name', $companyName))
            ->where('name', $projectName)
            ->exists();

        return $exists ? 'valid' : 'missing';
    }

    /**
     * @return array{exists: bool, valid: bool}
     */
    private function resolveUsername(string $username, ImportValidationContext $context): array
    {
        if (isset($context->usersSeen[$username])) {
            return ['exists' => true, 'valid' => $context->usersSeen[$username]['valid']];
        }

        return ['exists' => User::where('username', $username)->exists(), 'valid' => true];
    }

    /**
     * @param  array<int, \BackedEnum&object{label(): string}>  $cases
     */
    private function matchesLabel(string $value, array $cases): bool
    {
        $normalized = strtolower(trim($value));

        foreach ($cases as $case) {
            if (strtolower($case->label()) === $normalized) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array{row_number: int, cells: array<string, ?string>}  $row
     * @return array<string, mixed>
     */
    private function row(string $sheetName, array $row, string $action, string $status, ?string $message = null): array
    {
        return [
            'sheet_name' => $sheetName,
            'row_number' => $row['row_number'],
            'raw_data' => $row['cells'],
            'resolved_action' => $action,
            'validation_status' => $status,
            'validation_message' => $message,
        ];
    }

    /**
     * @param  array{row_number: int, cells: array<string, ?string>}  $row
     * @return array<string, mixed>
     */
    private function errorRow(string $sheetName, array $row, string $message): array
    {
        return $this->row($sheetName, $row, 'blocked', 'error', $message);
    }

    /**
     * @param  array{row_number: int, cells: array<string, ?string>}  $row
     * @return array<string, mixed>
     */
    private function blockedRow(string $sheetName, array $row, string $message): array
    {
        return $this->row($sheetName, $row, 'blocked', 'error', $message);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertRows(ImportBatch $batch, string $sheetName, array $rows, ImportValidationContext $context): void
    {
        foreach ($rows as $entry) {
            $username = $entry['__username'] ?? null;
            unset($entry['__username']);

            $importRow = $batch->importRows()->create($entry);

            // crossValidateUserTypeAgainstRoles() needs this id to
            // retroactively mark a Users row invalid once the Company
            // Roles sheet (processed after Users) is fully known.
            if ($sheetName === 'Users' && $username !== null && isset($context->usersSeen[$username])) {
                $context->usersSeen[$username]['importRowId'] = $importRow->id;
            }
        }
    }
}
