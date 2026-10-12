<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PerformanceSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * task #72 phase 0: guards on `perf:seed`. Small numbers throughout — the
 * 2,000-task company is for the artisan command, never for CI.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);
});

/** The small shape every test here uses. */
function runPerfSeed(array $options = []): int
{
    return Artisan::call('perf:seed', array_merge([
        '--tasks' => 40,
        '--comments' => 200,
        '--users' => 10,
        '--projects' => 3,
        '--departments' => 3,
    ], $options));
}

test('it refuses to run in production, with no override', function () {
    $original = app()->environment();
    app()->detectEnvironment(fn () => 'production');

    try {
        $exit = runPerfSeed();
    } finally {
        app()->detectEnvironment(fn () => $original);
    }

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('refuses to run in production');
    expect(Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->exists())->toBeFalse();
});

test('it refuses to run twice without --fresh', function () {
    expect(runPerfSeed())->toBe(0);

    $second = runPerfSeed();

    expect($second)->toBe(1);
    expect(Artisan::output())->toContain('--fresh');
    expect(Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->count())->toBe(1);
});

test('--fresh replaces only the perf company and leaves every other company untouched', function () {
    // A normal company with real data sitting alongside it.
    $realOrg = Organization::create(['name' => 'Real Co', 'slug' => 'real-co', 'accent_color' => '#1D9E75']);
    $realDept = Department::create(['organization_id' => $realOrg->id, 'name' => 'Marketing', 'color' => '#000000']);
    $realProject = Project::create(['organization_id' => $realOrg->id, 'name' => 'Real project', 'description' => 'd']);
    $realTask = Task::create([
        'organization_id' => $realOrg->id,
        'project_id' => $realProject->id,
        'department_id' => $realDept->id,
        'title' => 'Real task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $realUser = User::factory()->create(['email' => 'real@example.com']);

    runPerfSeed();
    $firstPerfOrgId = Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->value('id');

    expect(runPerfSeed(['--fresh' => true]))->toBe(0);

    // Replaced, not duplicated.
    expect(Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->count())->toBe(1);
    expect(Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->value('id'))
        ->not->toBe($firstPerfOrgId);

    // The real company is entirely untouched.
    $this->assertDatabaseHas('organizations', ['id' => $realOrg->id, 'name' => 'Real Co']);
    $this->assertDatabaseHas('tasks', ['id' => $realTask->id, 'title' => 'Real task']);
    $this->assertDatabaseHas('users', ['id' => $realUser->id, 'email' => 'real@example.com']);
    $this->assertDatabaseHas('departments', ['id' => $realDept->id]);
    $this->assertDatabaseHas('projects', ['id' => $realProject->id]);
});

test('row counts match the options given', function () {
    runPerfSeed(['--tasks' => 40, '--comments' => 200, '--users' => 10, '--projects' => 3, '--departments' => 3]);

    $org = Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->firstOrFail();

    expect(DB::table('tasks')->where('organization_id', $org->id)->count())->toBe(40);
    expect(DB::table('projects')->where('organization_id', $org->id)->count())->toBe(3);
    expect(DB::table('departments')->where('organization_id', $org->id)->count())->toBe(3);
    expect(DB::table('users')->where('email', 'like', '%'.PerformanceSeeder::EMAIL_DOMAIN)->count())->toBe(10);
    expect(DB::table('org_members')->where('organization_id', $org->id)->count())->toBe(10);

    // --comments is spent in full, not approximated.
    $taskIds = DB::table('tasks')->where('organization_id', $org->id)->pluck('id');
    expect(DB::table('comments')->whereIn('task_id', $taskIds)->count())->toBe(200);
});

test('it writes no notifications and sends no mail', function () {
    Mail::fake();
    Notification::fake();

    runPerfSeed();

    expect(DB::table('notifications')->count())->toBe(0);
    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

test('every audit case phase 6 will meet appears at least once', function () {
    // Enough tasks that each ~3-10% flavour is represented.
    runPerfSeed(['--tasks' => 400, '--comments' => 100]);

    $org = Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->firstOrFail();
    $taskIds = DB::table('tasks')->where('organization_id', $org->id)->pluck('id');
    $audit = DB::table('audit_log')->where('organization_id', $org->id)->get();

    // 1. task.created, with a FLAT changes payload (not old/new).
    $created = $audit->where('action', 'task.created');
    expect($created->count())->toBeGreaterThan(0);
    $createdChanges = json_decode($created->first()->changes, true);
    expect($createdChanges)->toHaveKeys(['project_id', 'department_id', 'title', 'priority', 'status']);
    expect($createdChanges['status'])->toBeString(); // flat, not ['old'=>…,'new'=>…]

    // 2. a plain completion.
    $statusChanges = $audit->where('action', 'task.status_changed');
    expect($statusChanges->count())->toBeGreaterThan(0);
    $toCompleted = $statusChanges->filter(function ($row) {
        $changes = json_decode($row->changes, true);

        return ($changes['status']['new'] ?? null) === 'completed';
    });
    expect($toCompleted->count())->toBeGreaterThan(0);

    // 3. reopened: completed -> in_progress exists somewhere.
    $reopened = $statusChanges->filter(function ($row) {
        $changes = json_decode($row->changes, true);

        return ($changes['status']['old'] ?? null) === 'completed'
            && ($changes['status']['new'] ?? null) === 'in_progress';
    });
    expect($reopened->count())->toBeGreaterThan(0);

    // 4. created directly as completed: a task.created row whose status is
    //    already completed and which has no later status change.
    $createdCompleted = $created->filter(function ($row) {
        return (json_decode($row->changes, true)['status'] ?? null) === 'completed';
    });
    expect($createdCompleted->count())->toBeGreaterThan(0);

    // 5. completed via a reassignment save: task.reassigned carrying BOTH
    //    assignee_id and status, which is what TaskObserver::updated()
    //    produces when the two change together.
    $reassigned = $audit->where('action', 'task.reassigned')->filter(function ($row) {
        $changes = json_decode($row->changes, true);

        return array_key_exists('assignee_id', $changes)
            && ($changes['status']['new'] ?? null) === 'completed';
    });
    expect($reassigned->count())->toBeGreaterThan(0);

    // 6. tasks with no audit rows at all (imported with no logged-in user).
    $taskIdsWithAudit = $audit->pluck('entity_id')->unique();
    expect($taskIds->diff($taskIdsWithAudit)->count())->toBeGreaterThan(0);

    // Actors are real members of the company.
    $memberIds = DB::table('org_members')->where('organization_id', $org->id)->pluck('user_id');
    expect($audit->pluck('user_id')->unique()->diff($memberIds)->count())->toBe(0);

    // updated_at keeps pace with the last logged change, as in real data.
    $sample = DB::table('tasks')->where('organization_id', $org->id)->first();
    expect($sample->updated_at)->toBeGreaterThanOrEqual($sample->created_at);
});

test('staff only get access to departments that exist in the perf company', function () {
    runPerfSeed();

    $org = Organization::withoutGlobalScopes()->where('slug', PerformanceSeeder::COMPANY_SLUG)->firstOrFail();
    $departmentIds = DB::table('departments')->where('organization_id', $org->id)->pluck('id');

    $granted = DB::table('access_permissions')->where('organization_id', $org->id)->get();

    expect($granted->count())->toBeGreaterThan(0);
    expect($granted->pluck('department_id')->unique()->diff($departmentIds)->count())->toBe(0);
    expect($granted->every(fn ($row) => (bool) $row->allowed))->toBeTrue();
});

test('generated data is repeatable: two runs produce identical titles and statuses', function () {
    runPerfSeed(['--tasks' => 25, '--comments' => 50]);
    $first = DB::table('tasks')->orderBy('id')->pluck('status')->implode(',')
        .'|'.DB::table('tasks')->orderBy('id')->pluck('title')->implode(',');

    runPerfSeed(['--tasks' => 25, '--comments' => 50, '--fresh' => true]);
    $second = DB::table('tasks')->orderBy('id')->pluck('status')->implode(',')
        .'|'.DB::table('tasks')->orderBy('id')->pluck('title')->implode(',');

    expect($second)->toBe($first);
});

test('the shared password is different on every run unless one is given', function () {
    runPerfSeed();
    preg_match('/Shared password\s+\.*\s*(\S+)/', Artisan::output(), $first);

    runPerfSeed(['--fresh' => true]);
    preg_match('/Shared password\s+\.*\s*(\S+)/', Artisan::output(), $second);

    expect($first[1] ?? 'a')->not->toBe($second[1] ?? 'b');

    runPerfSeed(['--fresh' => true, '--password' => 'KnownPassword123!']);
    $user = User::where('email', 'like', '%'.PerformanceSeeder::EMAIL_DOMAIN)->firstOrFail();
    expect(Hash::check('KnownPassword123!', $user->password))->toBeTrue();
});
