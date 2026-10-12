<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * task #72 phase 0: generates one large, realistic company so the Task
 * list / Kanban / Dashboard pages can be measured against something that
 * behaves like production. Driven by `php artisan perf:seed` — deliberately
 * NOT registered in DatabaseSeeder, since nothing about normal setup should
 * ever create 2,000 tasks.
 *
 * Three rules shape every choice in here:
 *
 * 1. NOTHING may fire. Every write is a raw chunked DB::table()->insert(),
 *    never Eloquent — Task, Comment and Subtask all carry #[ObservedBy],
 *    and TaskObserver hands each audit row to AuditEventNotifier, which
 *    calls $user->notify() on both the database AND mail channels. Raw
 *    inserts make "no notifications, no mail" structural rather than
 *    something a future edit could quietly undo.
 *
 * 2. Repeatable. mt_srand(SEED) at the start, and every generated value
 *    derives from mt_rand() alone, so two developers get byte-identical
 *    data. The per-run password is the one deliberate exception — it uses
 *    random_bytes() precisely BECAUSE it must not be predictable.
 *
 * 3. Scoped. Everything belongs to one company ("Perf Test Co"), and the
 *    users it creates are identifiable by their @perf-test.invalid email
 *    domain. purge() never touches a row outside that set.
 */
class PerformanceSeeder extends Seeder
{
    public const COMPANY_NAME = 'Perf Test Co';

    public const COMPANY_SLUG = 'perf-test-co';

    /** Reserved TLD (RFC 2606) — these addresses can never be deliverable. */
    public const EMAIL_DOMAIN = '@perf-test.invalid';

    /**
     * Prefixes the app itself never generates: EmployeeIdGenerator issues
     * EMP-/CLIENT- ids, so a PERF- id can't collide with a real one, and
     * "perfNNNN" usernames stay clear of anything a human would pick.
     */
    public const EMPLOYEE_ID_PREFIX = 'PERF-';

    public const USERNAME_PREFIX = 'perf';

    private const SEED = 42;

    private const CHUNK = 500;

    /** @var array<int, string> */
    private const FIRST_NAMES = [
        'Ayu', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko',
        'Kartika', 'Lina', 'Made', 'Nina', 'Oka', 'Putri', 'Rama', 'Sari', 'Tono', 'Wayan',
        'Alex', 'Brooke', 'Casey', 'Devon', 'Elliot', 'Frankie', 'Harper', 'Jordan', 'Kai', 'Logan',
    ];

    /** @var array<int, string> */
    private const LAST_NAMES = [
        'Wibowo', 'Santoso', 'Pratama', 'Kusuma', 'Hartono', 'Nugroho', 'Setiawan', 'Halim',
        'Suryadi', 'Gunawan', 'Carter', 'Delgado', 'Fletcher', 'Mercer', 'Okafor', 'Vance',
    ];

    /** @var array<int, string> */
    private const TITLE_VERBS = [
        'Draft', 'Review', 'Publish', 'Refresh', 'Audit', 'Migrate', 'Prepare', 'Finalise',
        'Schedule', 'Rework', 'Translate', 'Approve', 'Archive', 'Rebuild', 'Consolidate',
    ];

    /** @var array<int, string> */
    private const TITLE_NOUNS = [
        'onboarding deck', 'quarterly report', 'landing page copy', 'invoice template',
        'student handbook', 'campaign brief', 'pricing sheet', 'partner agreement',
        'support macros', 'tutor roster', 'intake form', 'welcome email', 'lesson plan',
        'budget forecast', 'brand guidelines', 'feedback survey', 'release notes',
    ];

    /** @var array<int, string> */
    private const SENTENCES = [
        'Agreed in the weekly sync, so the deadline is firm.',
        'Blocked until the finance team confirms the numbers.',
        'Please keep the existing structure and only update the wording.',
        'This replaces the version we circulated last month.',
        'Check with the Bali office before publishing anything externally.',
        'Low priority, but it keeps coming up, so let us close it out.',
        'Needs a second pair of eyes before it goes to the client.',
    ];

    /** @var array<int, string> */
    private const DEPARTMENT_NAMES = [
        'Marketing', 'Operations', 'Sales', 'Training', 'Technology', 'Biz Dev', 'Finance', 'Support',
    ];

    /** @var array<int, string> */
    private const DEPARTMENT_COLORS = [
        '#534AB7', '#1D9E75', '#D97706', '#DC2626', '#0891B2', '#7C3AED', '#65A30D', '#DB2777',
    ];

    /** @var array<int, string> */
    private const PROJECT_NOUNS = [
        'Website Refresh', 'Term 1 Intake', 'Partner Onboarding', 'Brand Refresh', 'Support Revamp',
        'Tutor Recruitment', 'Payments Migration', 'Content Library', 'Mobile App', 'Alumni Programme',
        'Scholarship Drive', 'Course Catalogue',
    ];

    /** @var array<int, string> */
    private const EMOJIS = ['👍', '🎉', '❤️', '😄', '🚀', '👀'];

    /** @var array<string, int> */
    private array $counts = [];

    private string $password = '';

    /** @var array<string, array<int, int>> role slug => user ids */
    private array $usersByRole = ['management' => [], 'staff' => [], 'client' => []];

    /**
     * @param  array{tasks:int, comments:int, users:int, projects:int, departments:int, password:?string}  $options
     * @return array{counts: array<string, int>, password: string, organization: Organization, logins: array<string, string>}
     */
    public function generate(array $options): array
    {
        mt_srand(self::SEED);

        $this->password = $options['password'] ?: $this->randomPassword();
        $hashed = Hash::make($this->password);

        $organization = $this->createOrganization();
        $departments = $this->createDepartments($organization, $options['departments']);
        $projects = $this->createProjects($organization, $options['projects']);
        $users = $this->createUsers($organization, $projects, $departments, $options['users'], $hashed);

        $eligibility = $this->buildEligibility($projects, $departments, $users);

        $tasks = $this->createTasks($organization, $projects, $departments, $eligibility, $options['tasks']);
        $this->createSubtasks($tasks, $eligibility);
        $comments = $this->createComments($tasks, $users, $options['comments']);
        $this->createCommentExtras($comments, $users);
        $this->createAuditHistory($organization, $tasks, $users);

        return [
            'counts' => $this->counts,
            'password' => $this->password,
            'organization' => $organization,
            'logins' => $this->exampleLogins(),
        ];
    }

    /**
     * Deletes the perf company and everything it owns, in FK-safe order,
     * using raw deletes so soft-deleted tasks go too (Eloquent's cascade
     * would skip them, and the FKs on comments.user_id / audit_log.user_id
     * are plain constrained() — RESTRICT — so users must come last).
     */
    public function purge(): void
    {
        $organization = Organization::withoutGlobalScopes()->where('slug', self::COMPANY_SLUG)->first();
        $userIds = DB::table('users')->where('email', 'like', '%'.self::EMAIL_DOMAIN)->pluck('id')->all();

        if ($organization === null && $userIds === []) {
            return;
        }

        DB::transaction(function () use ($organization, $userIds) {
            if ($organization !== null) {
                $taskIds = DB::table('tasks')->where('organization_id', $organization->id)->pluck('id')->all();
                $commentIds = $taskIds === [] ? [] : DB::table('comments')->whereIn('task_id', $taskIds)->pluck('id')->all();

                if ($commentIds !== []) {
                    DB::table('comment_reactions')->whereIn('comment_id', $commentIds)->delete();
                    DB::table('comment_mentions')->whereIn('comment_id', $commentIds)->delete();
                    // Replies reference their parent, so break the self-FK
                    // before deleting rather than relying on delete order.
                    DB::table('comments')->whereIn('id', $commentIds)->update(['parent_comment_id' => null]);
                    DB::table('comments')->whereIn('id', $commentIds)->delete();
                }

                if ($taskIds !== []) {
                    DB::table('subtasks')->whereIn('task_id', $taskIds)->delete();
                    DB::table('task_documents')->whereIn('task_id', $taskIds)->delete();
                    DB::table('task_folder_links')->whereIn('task_id', $taskIds)->delete();
                }

                DB::table('audit_log')->where('organization_id', $organization->id)->delete();
                DB::table('tasks')->where('organization_id', $organization->id)->delete();

                $projectIds = DB::table('projects')->where('organization_id', $organization->id)->pluck('id')->all();
                if ($projectIds !== []) {
                    DB::table('project_staff')->whereIn('project_id', $projectIds)->delete();
                    DB::table('project_clients')->whereIn('project_id', $projectIds)->delete();
                    DB::table('projects')->whereIn('id', $projectIds)->delete();
                }

                DB::table('access_permissions')->where('organization_id', $organization->id)->delete();
                DB::table('org_members')->where('organization_id', $organization->id)->delete();
                DB::table('departments')->where('organization_id', $organization->id)->delete();
            }

            if ($userIds !== []) {
                // Someone may have clicked around on dev as a perf user.
                DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->whereIn('notifiable_id', $userIds)
                    ->delete();
                DB::table('sessions')->whereIn('user_id', $userIds)->delete();
                DB::table('audit_log')->whereIn('user_id', $userIds)->delete();
                DB::table('user_roles')->whereIn('user_id', $userIds)->delete();
                DB::table('org_members')->whereIn('user_id', $userIds)->delete();
                DB::table('access_permissions')->whereIn('user_id', $userIds)->delete();
                DB::table('project_staff')->whereIn('user_id', $userIds)->delete();
                DB::table('project_clients')->whereIn('user_id', $userIds)->delete();
                DB::table('users')->whereIn('id', $userIds)->delete();
            }

            if ($organization !== null) {
                DB::table('organizations')->where('id', $organization->id)->delete();
            }
        });
    }

    public static function companyExists(): bool
    {
        return Organization::withoutGlobalScopes()->where('slug', self::COMPANY_SLUG)->exists();
    }

    private function createOrganization(): Organization
    {
        $id = DB::table('organizations')->insertGetId([
            'name' => self::COMPANY_NAME,
            'slug' => self::COMPANY_SLUG,
            'accent_color' => '#1D9E75',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->counts['organizations'] = 1;

        return Organization::withoutGlobalScopes()->findOrFail($id);
    }

    /** @return array<int, array{id:int, name:string}> */
    private function createDepartments(Organization $organization, int $count): array
    {
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $rows[] = [
                'organization_id' => $organization->id,
                'name' => self::DEPARTMENT_NAMES[$i % count(self::DEPARTMENT_NAMES)].($i >= count(self::DEPARTMENT_NAMES) ? ' '.(intdiv($i, count(self::DEPARTMENT_NAMES)) + 1) : ''),
                'color' => self::DEPARTMENT_COLORS[$i % count(self::DEPARTMENT_COLORS)],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('departments')->insert($rows);
        $this->counts['departments'] = count($rows);

        return DB::table('departments')->where('organization_id', $organization->id)
            ->get(['id', 'name'])->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->all();
    }

    /** @return array<int, array{id:int, name:string}> */
    private function createProjects(Organization $organization, int $count): array
    {
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $rows[] = [
                'organization_id' => $organization->id,
                'name' => self::PROJECT_NOUNS[$i % count(self::PROJECT_NOUNS)].($i >= count(self::PROJECT_NOUNS) ? ' '.(intdiv($i, count(self::PROJECT_NOUNS)) + 1) : ''),
                'description' => 'Generated project for performance testing.',
                'is_external' => false,
                'status' => 'open',
                'priority' => ['high', 'medium', 'low'][mt_rand(0, 2)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('projects')->insert($rows);
        $this->counts['projects'] = count($rows);

        return DB::table('projects')->where('organization_id', $organization->id)
            ->get(['id', 'name'])->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->all();
    }

    /**
     * @param  array<int, array{id:int, name:string}>  $projects
     * @param  array<int, array{id:int, name:string}>  $departments
     * @return array<int, array{id:int, role:string, projects:array<int,int>, departments:array<int,int>}>
     */
    private function createUsers(Organization $organization, array $projects, array $departments, int $count, string $hashedPassword): array
    {
        $roleIds = DB::table('roles')->pluck('id', 'slug')->all();

        $managementCount = max(1, (int) round($count * 0.10));
        $clientCount = max(1, (int) round($count * 0.20));
        $staffCount = max(1, $count - $managementCount - $clientCount);

        $userRows = [];
        $plan = [];
        $index = 0;

        foreach (['management' => $managementCount, 'staff' => $staffCount, 'client' => $clientCount] as $role => $roleCount) {
            for ($i = 0; $i < $roleCount; $i++) {
                $index++;
                $number = str_pad((string) $index, 4, '0', STR_PAD_LEFT);
                $userRows[] = [
                    'username' => self::USERNAME_PREFIX.$number,
                    'name' => self::FIRST_NAMES[mt_rand(0, count(self::FIRST_NAMES) - 1)].' '.self::LAST_NAMES[mt_rand(0, count(self::LAST_NAMES) - 1)],
                    'employee_id' => self::EMPLOYEE_ID_PREFIX.$number,
                    'email' => self::USERNAME_PREFIX.$number.self::EMAIL_DOMAIN,
                    'phone' => '+6281'.str_pad((string) mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'password' => $hashedPassword,
                    'must_change_password' => false,
                    'auth_provider' => 'password',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $plan[] = ['role' => $role, 'username' => self::USERNAME_PREFIX.$number];
            }
        }

        foreach (array_chunk($userRows, self::CHUNK) as $chunk) {
            DB::transaction(fn () => DB::table('users')->insert($chunk));
        }
        $this->counts['users'] = count($userRows);

        $idByUsername = DB::table('users')
            ->whereIn('username', array_column($plan, 'username'))
            ->pluck('id', 'username')->all();

        $members = [];
        $permissions = [];
        $projectStaff = [];
        $projectClients = [];
        $users = [];

        foreach ($plan as $entry) {
            $userId = $idByUsername[$entry['username']];
            $role = $entry['role'];
            $this->usersByRole[$role][] = $userId;

            $members[] = [
                'organization_id' => $organization->id,
                'user_id' => $userId,
                'role_id' => $roleIds[$role],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $userProjects = [];
            $userDepartments = [];

            if ($role === 'staff') {
                foreach ($this->pick($departments, mt_rand(1, min(3, count($departments)))) as $department) {
                    $userDepartments[] = $department['id'];
                    $permissions[] = [
                        'user_id' => $userId,
                        'organization_id' => $organization->id,
                        'department_id' => $department['id'],
                        'allowed' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                foreach ($this->pick($projects, mt_rand(2, min(5, count($projects)))) as $project) {
                    $userProjects[] = $project['id'];
                    $projectStaff[] = ['project_id' => $project['id'], 'user_id' => $userId];
                }
            }

            if ($role === 'client') {
                foreach ($this->pick($projects, mt_rand(1, min(2, count($projects)))) as $project) {
                    $userProjects[] = $project['id'];
                    $projectClients[] = ['project_id' => $project['id'], 'user_id' => $userId];
                }
            }

            $users[] = ['id' => $userId, 'role' => $role, 'projects' => $userProjects, 'departments' => $userDepartments];
        }

        $this->insertChunked('org_members', $members);
        $this->insertChunked('access_permissions', $permissions);
        $this->insertChunked('project_staff', $projectStaff);
        $this->insertChunked('project_clients', $projectClients);

        return $users;
    }

    /**
     * Mirrors Task::eligibleAssigneesFor() exactly — management in the
     * company (no department restriction), the project's clients, and
     * staff holding BOTH project_staff membership and active access to
     * that department — but resolved in PHP from what we just generated,
     * so it costs no queries per (project, department) pair.
     *
     * @param  array<int, array{id:int, name:string}>  $projects
     * @param  array<int, array{id:int, name:string}>  $departments
     * @param  array<int, array{id:int, role:string, projects:array<int,int>, departments:array<int,int>}>  $users
     * @return array<string, array<int, int>> "projectId:departmentId" => user ids
     */
    private function buildEligibility(array $projects, array $departments, array $users): array
    {
        $eligibility = [];

        foreach ($projects as $project) {
            foreach ($departments as $department) {
                $eligible = [];

                foreach ($users as $user) {
                    $qualifies = match ($user['role']) {
                        'management' => true,
                        'client' => in_array($project['id'], $user['projects'], true),
                        default => in_array($project['id'], $user['projects'], true)
                            && in_array($department['id'], $user['departments'], true),
                    };

                    if ($qualifies) {
                        $eligible[] = $user['id'];
                    }
                }

                $eligibility[$project['id'].':'.$department['id']] = $eligible;
            }
        }

        return $eligibility;
    }

    /**
     * @param  array<int, array{id:int, name:string}>  $projects
     * @param  array<int, array{id:int, name:string}>  $departments
     * @param  array<string, array<int, int>>  $eligibility
     * @return array<int, array{id:int, status:string, assignee_id:?int, project_id:int, department_id:int, title:string, priority:string, due_date:?string, start_date:?string, created_at:string}>
     */
    private function createTasks(Organization $organization, array $projects, array $departments, array $eligibility, int $count): array
    {
        $rows = [];
        $meta = [];

        for ($i = 0; $i < $count; $i++) {
            $project = $projects[mt_rand(0, count($projects) - 1)];
            $department = $departments[mt_rand(0, count($departments) - 1)];
            $status = $this->weighted(['pending' => 25, 'in_progress' => 20, 'in_review' => 10, 'completed' => 45]);
            $priority = ['high', 'medium', 'low'][mt_rand(0, 2)];

            $createdAt = now()->subDays(mt_rand(0, 365))->subMinutes(mt_rand(0, 1440));

            $candidates = $eligibility[$project['id'].':'.$department['id']] ?? [];
            $assigneeId = ($candidates === [] || mt_rand(1, 100) <= 5)
                ? null
                : $candidates[mt_rand(0, count($candidates) - 1)];

            $dueDate = mt_rand(1, 100) <= 10
                ? null
                : now()->addDays(mt_rand(-180, 90))->toDateString();

            // TaskObserver::saving() sets start_date on the transition into
            // in_progress, so anything that has been started carries one.
            $startDate = in_array($status, ['in_progress', 'in_review', 'completed'], true)
                ? $createdAt->copy()->addDays(mt_rand(0, 14))->toDateString()
                : null;

            $deletedAt = mt_rand(1, 100) <= 3 ? $createdAt->copy()->addDays(mt_rand(1, 30)) : null;

            $rows[] = [
                'organization_id' => $organization->id,
                'project_id' => $project['id'],
                'department_id' => $department['id'],
                'assignee_id' => $assigneeId,
                'title' => $this->title(),
                'description' => '<p>'.$this->sentence().'</p>',
                'priority' => $priority,
                'status' => $status,
                'due_date' => $dueDate,
                'start_date' => $startDate,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => $deletedAt,
            ];

            $meta[] = [
                'status' => $status,
                'assignee_id' => $assigneeId,
                'project_id' => $project['id'],
                'department_id' => $department['id'],
                'priority' => $priority,
                'due_date' => $dueDate,
                'start_date' => $startDate,
                'created_at' => $createdAt,
                'title' => $rows[$i]['title'],
                'eligible' => $candidates,
            ];
        }

        $firstId = $this->insertChunkedWithIds('tasks', $rows);
        $this->counts['tasks'] = count($rows);

        $tasks = [];
        foreach ($meta as $offset => $entry) {
            $tasks[] = ['id' => $firstId + $offset] + $entry;
        }

        return $tasks;
    }

    /**
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, array<int, int>>  $eligibility
     */
    private function createSubtasks(array $tasks, array $eligibility): void
    {
        $rows = [];

        foreach ($tasks as $task) {
            // 0-5, averaging ~2.
            $subtaskCount = $this->weighted([0 => 20, 1 => 20, 2 => 25, 3 => 20, 4 => 10, 5 => 5]);
            $candidates = $eligibility[$task['project_id'].':'.$task['department_id']] ?? [];

            for ($i = 0; $i < (int) $subtaskCount; $i++) {
                $rows[] = [
                    'task_id' => $task['id'],
                    'title' => $this->title(),
                    'description' => mt_rand(1, 100) <= 30 ? $this->sentence() : null,
                    'assignee_id' => ($candidates !== [] && mt_rand(1, 100) <= 60)
                        ? $candidates[mt_rand(0, count($candidates) - 1)]
                        : null,
                    'is_done' => $task['status'] === 'completed' ? mt_rand(1, 100) <= 85 : mt_rand(1, 100) <= 30,
                    'due_date' => mt_rand(1, 100) <= 60 ? now()->addDays(mt_rand(-120, 60))->toDateString() : null,
                    'start_date' => mt_rand(1, 100) <= 40 ? $task['created_at']->copy()->addDays(mt_rand(0, 10))->toDateString() : null,
                    'created_at' => $task['created_at'],
                    'updated_at' => $task['created_at'],
                ];
            }
        }

        $this->insertChunked('subtasks', $rows);
        $this->counts['subtasks'] = count($rows);
    }

    /**
     * Skewed on purpose: most tasks have a handful of comments and a few
     * have dozens, which is what makes the per-row eager loading on the
     * Tasks list hurt.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<int, array{id:int, role:string, projects:array<int,int>, departments:array<int,int>}>  $users
     * @return array<int, array{id:int, task_id:int, user_id:int}>
     */
    private function createComments(array $tasks, array $users, int $target): array
    {
        $userIds = array_column($users, 'id');
        $rows = [];
        $parents = [];

        // A tenth of tasks are "busy" and soak up a disproportionate share.
        $busyCount = max(1, (int) round(count($tasks) * 0.10));
        $busyIndexes = array_flip($this->pickIndexes(count($tasks), $busyCount));

        // Passes over the task list until the whole budget is spent, so
        // --comments means what it says. One pass alone lands ~20% short
        // at the default sizes, which would quietly make two runs with
        // different task counts incomparable.
        $remaining = $target;
        $pass = 0;

        while ($remaining > 0 && $pass < 50) {
            foreach ($tasks as $index => $task) {
                if ($remaining <= 0) {
                    break;
                }

                $share = isset($busyIndexes[$index]) ? mt_rand(30, 80) : mt_rand(0, 5);
                $share = min($share, $remaining);

                for ($i = 0; $i < $share; $i++) {
                    $createdAt = $task['created_at']->copy()->addMinutes(mt_rand(10, 60 * 24 * 60));
                    $rows[] = [
                        'task_id' => $task['id'],
                        'parent_comment_id' => null,
                        'user_id' => $userIds[mt_rand(0, count($userIds) - 1)],
                        'body' => '<p>'.$this->sentence().'</p>',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                    $parents[] = ['task_index' => $index, 'offset' => count($rows) - 1];
                }

                $remaining -= $share;
            }

            $pass++;
        }

        $firstId = $this->insertChunkedWithIds('comments', $rows);
        $this->counts['comments'] = count($rows);

        $comments = [];
        foreach ($rows as $offset => $row) {
            $comments[] = ['id' => $firstId + $offset, 'task_id' => $row['task_id'], 'user_id' => $row['user_id']];
        }

        $this->makeReplies($comments);

        return $comments;
    }

    /**
     * ~20% of comments become replies. Done as an UPDATE after the fact so
     * every parent id is already known, and only ever pointing at another
     * comment on the SAME task — CommentController::resolveParentCommentId()
     * always re-parents to a top-level ancestor, so no reply-of-a-reply.
     *
     * @param  array<int, array{id:int, task_id:int, user_id:int}>  $comments
     */
    private function makeReplies(array $comments): void
    {
        $byTask = [];
        foreach ($comments as $comment) {
            $byTask[$comment['task_id']][] = $comment['id'];
        }

        $updates = [];
        foreach ($byTask as $ids) {
            if (count($ids) < 2) {
                continue;
            }

            $parentId = $ids[0];
            foreach (array_slice($ids, 1) as $id) {
                if (mt_rand(1, 100) <= 20) {
                    $updates[$parentId][] = $id;
                }
            }
        }

        $replies = 0;
        foreach ($updates as $parentId => $childIds) {
            DB::table('comments')->whereIn('id', $childIds)->update(['parent_comment_id' => $parentId]);
            $replies += count($childIds);
        }

        $this->counts['comment replies'] = $replies;
    }

    /**
     * @param  array<int, array{id:int, task_id:int, user_id:int}>  $comments
     * @param  array<int, array{id:int, role:string, projects:array<int,int>, departments:array<int,int>}>  $users
     */
    private function createCommentExtras(array $comments, array $users): void
    {
        $userIds = array_column($users, 'id');
        $mentions = [];
        $reactions = [];

        foreach ($comments as $comment) {
            if (mt_rand(1, 100) <= 15) {
                foreach ($this->pick($userIds, mt_rand(1, min(2, count($userIds)))) as $userId) {
                    $mentions[] = ['comment_id' => $comment['id'], 'user_id' => $userId];
                }
            }

            if (mt_rand(1, 100) <= 20) {
                // unique(comment_id, user_id): distinct users, never
                // multiple reactions from the same person.
                foreach ($this->pick($userIds, mt_rand(1, min(3, count($userIds)))) as $userId) {
                    $reactions[] = [
                        'comment_id' => $comment['id'],
                        'user_id' => $userId,
                        'emoji' => self::EMOJIS[mt_rand(0, count(self::EMOJIS) - 1)],
                        'created_at' => now(),
                    ];
                }
            }
        }

        $this->insertChunked('comment_mentions', $mentions);
        $this->insertChunked('comment_reactions', $reactions);
        $this->counts['comment mentions'] = count($mentions);
        $this->counts['comment reactions'] = count($reactions);
    }

    /**
     * Audit rows in exactly the shapes TaskObserver writes, because phase 6
     * will backfill a "completed date" from this history and must meet
     * every case that exists in real data:
     *
     *   - task.created  — FLAT initial attributes (not old/new)
     *   - task.status_changed — {"status":{"old":…,"new":…}}
     *   - task.reassigned — fires when assignee_id changes and OUTRANKS
     *     status in TaskObserver::updated()'s match(), so a save that
     *     changed both is logged under this action with both keys
     *
     * Distribution across completed tasks: ~10% completed→reopened→
     * completed (the LAST row is the real completion), ~5% created already
     * completed, ~5% completed via a reassignment save, ~3% no audit rows
     * at all (TaskObserver::log() returns early when auth()->check() is
     * false, e.g. an import), the rest a single status change.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<int, array{id:int, role:string, projects:array<int,int>, departments:array<int,int>}>  $users
     */
    private function createAuditHistory(Organization $organization, array $tasks, array $users): void
    {
        $actorIds = array_values(array_merge($this->usersByRole['management'], $this->usersByRole['staff']));
        $rows = [];
        $lastTouched = [];
        $flavourCounts = [];

        foreach ($tasks as $task) {
            $actor = $actorIds[mt_rand(0, count($actorIds) - 1)];
            $completed = $task['status'] === 'completed';
            $flavour = $completed ? $this->completionFlavour() : 'plain';
            $flavourCounts[$flavour] = ($flavourCounts[$flavour] ?? 0) + 1;

            if ($flavour === 'unlogged') {
                $lastTouched[$task['id']] = $task['created_at'];

                continue;
            }

            $initialStatus = match ($flavour) {
                'created_completed' => 'completed',
                'plain' => $task['status'],
                default => 'pending',
            };

            $rows[] = $this->auditRow($organization, $actor, $task, 'task.created', [
                'project_id' => $task['project_id'],
                'department_id' => $task['department_id'],
                'assignee_id' => $task['assignee_id'],
                'title' => $task['title'],
                'priority' => $task['priority'],
                'status' => $initialStatus,
                'due_date' => $task['due_date'],
                'start_date' => $task['start_date'],
            ], $task['created_at']);

            $cursor = $task['created_at']->copy();
            $lastTouched[$task['id']] = $cursor->copy();

            if ($flavour === 'plain' || $flavour === 'created_completed') {
                continue;
            }

            $step = function (string $action, array $changes) use (&$cursor, &$rows, &$lastTouched, $organization, $actor, $task) {
                $cursor = $cursor->copy()->addHours(mt_rand(2, 24 * 20));
                $rows[] = $this->auditRow($organization, $actor, $task, $action, $changes, $cursor);
                $lastTouched[$task['id']] = $cursor->copy();
            };

            if ($flavour === 'reassigned') {
                $step('task.reassigned', [
                    'assignee_id' => ['old' => null, 'new' => $task['assignee_id']],
                    'status' => ['old' => 'in_progress', 'new' => 'completed'],
                ]);

                continue;
            }

            $step('task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']]);

            if ($flavour === 'reopened') {
                $step('task.status_changed', ['status' => ['old' => 'completed', 'new' => 'in_progress']]);
                $step('task.status_changed', ['status' => ['old' => 'in_progress', 'new' => 'completed']]);
            }
        }

        $this->insertChunked('audit_log', $rows);
        $this->counts['audit log rows'] = count($rows);
        foreach ($flavourCounts as $flavour => $count) {
            $this->counts['  tasks: '.$flavour] = $count;
        }

        $this->syncTaskUpdatedAt($lastTouched);
    }

    /**
     * Real tasks carry an updated_at at least as late as their last logged
     * change; leaving it at created_at would make the generated data
     * trivially distinguishable from production.
     *
     * @param  array<int, Carbon>  $lastTouched
     */
    private function syncTaskUpdatedAt(array $lastTouched): void
    {
        $byTimestamp = [];
        foreach ($lastTouched as $taskId => $timestamp) {
            $byTimestamp[$timestamp->toDateTimeString()][] = $taskId;
        }

        foreach (array_chunk($byTimestamp, 100, true) as $chunk) {
            DB::transaction(function () use ($chunk) {
                foreach ($chunk as $timestamp => $taskIds) {
                    DB::table('tasks')->whereIn('id', $taskIds)->update(['updated_at' => $timestamp]);
                }
            });
        }
    }

    /** @return array<string, mixed> */
    private function auditRow(Organization $organization, int $actorId, array $task, string $action, array $changes, Carbon $at): array
    {
        return [
            'organization_id' => $organization->id,
            'user_id' => $actorId,
            'action' => $action,
            'entity_type' => 'task',
            'entity_id' => $task['id'],
            'import_batch_id' => null,
            'changes' => json_encode($changes),
            'created_at' => $at,
        ];
    }

    private function completionFlavour(): string
    {
        $roll = mt_rand(1, 100);

        return match (true) {
            $roll <= 3 => 'unlogged',
            $roll <= 8 => 'created_completed',
            $roll <= 13 => 'reassigned',
            $roll <= 23 => 'reopened',
            default => 'plain',
        };
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function insertChunked(string $table, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, self::CHUNK) as $chunk) {
            DB::transaction(fn () => DB::table($table)->insert($chunk));
        }
    }

    /**
     * Same, but stamping an explicit id on every row and returning the
     * first one, so callers can map offset => id without a second query.
     *
     * Deliberately explicit rather than inferring ids from max(id) after
     * the fact: ids are not guaranteed contiguous or predictable (SQLite
     * reuses rowids freed by a delete, MySQL does not), and a wrong guess
     * silently produces children pointing at the wrong parents — which is
     * exactly what it did before this was fixed.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return int the id of the first row
     */
    private function insertChunkedWithIds(string $table, array &$rows): int
    {
        if ($rows === []) {
            return 0;
        }

        $firstId = (int) (DB::table($table)->max('id') ?? 0) + 1;

        foreach ($rows as $offset => $row) {
            $rows[$offset] = ['id' => $firstId + $offset] + $row;
        }

        $this->insertChunked($table, $rows);

        return $firstId;
    }

    /**
     * @template T
     *
     * @param  array<int, T>  $items
     * @return array<int, T>
     */
    private function pick(array $items, int $count): array
    {
        if ($items === []) {
            return [];
        }

        $indexes = $this->pickIndexes(count($items), min($count, count($items)));

        return array_map(fn (int $i) => $items[$i], $indexes);
    }

    /**
     * Distinct indexes, chosen with mt_rand only (shuffle() uses the same
     * generator but its ordering is not guaranteed stable across PHP
     * versions, which would break repeatability).
     *
     * @return array<int, int>
     */
    private function pickIndexes(int $total, int $count): array
    {
        $chosen = [];
        $guard = 0;

        while (count($chosen) < $count && $guard++ < $total * 10) {
            $candidate = mt_rand(0, $total - 1);
            if (! in_array($candidate, $chosen, true)) {
                $chosen[] = $candidate;
            }
        }

        return $chosen;
    }

    /** @param array<string|int, int> $weights value => relative weight */
    private function weighted(array $weights): string|int
    {
        $total = array_sum($weights);
        $roll = mt_rand(1, $total);

        foreach ($weights as $value => $weight) {
            $roll -= $weight;
            if ($roll <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }

    private function title(): string
    {
        return self::TITLE_VERBS[mt_rand(0, count(self::TITLE_VERBS) - 1)]
            .' '.self::TITLE_NOUNS[mt_rand(0, count(self::TITLE_NOUNS) - 1)];
    }

    private function sentence(): string
    {
        return self::SENTENCES[mt_rand(0, count(self::SENTENCES) - 1)];
    }

    /**
     * Deliberately NOT mt_rand: this one value must be unpredictable, since
     * the dev site is reachable from the internet. It is therefore also the
     * one value that differs between otherwise-identical runs.
     */
    private function randomPassword(): string
    {
        return substr(strtr(base64_encode(random_bytes(16)), '+/=', 'Aa1'), 0, 16);
    }

    /** @return array<string, string> */
    private function exampleLogins(): array
    {
        $logins = [];

        foreach ($this->usersByRole as $role => $ids) {
            if ($ids === []) {
                continue;
            }

            $email = DB::table('users')->where('id', $ids[0])->value('email');
            $logins[$role] = (string) $email;
        }

        return $logins;
    }
}
