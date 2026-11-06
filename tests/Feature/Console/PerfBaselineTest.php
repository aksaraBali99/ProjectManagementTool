<?php

use Database\Seeders\PerformanceSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 0: guards on `perf:baseline`. The command exists to
 * MEASURE, so the property that matters most is that running it changes
 * nothing.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    Artisan::call('perf:seed', [
        '--tasks' => 15,
        '--comments' => 40,
        '--users' => 8,
        '--projects' => 2,
        '--departments' => 2,
    ]);
});

/** @return array<string, int> table => row count */
function tableCounts(): array
{
    $tables = [
        'organizations', 'users', 'org_members', 'access_permissions', 'departments',
        'projects', 'project_staff', 'project_clients', 'tasks', 'subtasks',
        'comments', 'comment_mentions', 'comment_reactions', 'audit_log', 'notifications',
    ];

    $counts = [];
    foreach ($tables as $table) {
        $counts[$table] = DB::table($table)->count();
    }

    return $counts;
}

test('it exits 0 and prints a row per page with a numeric query count', function () {
    $exit = Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 1]);
    $output = Artisan::output();

    expect($exit)->toBe(0);

    foreach (['Dashboard', 'Kanban', 'Tasks list', 'Projects', 'Calendar'] as $page) {
        expect($output)->toContain($page);
    }

    // The environment banner, so saved tables are comparable later.
    expect($output)->toContain('APP_ENV')
        ->toContain('DB driver')
        ->toContain('PHP');

    // Every page row carries a numeric query count. The table renders as
    // "| Dashboard | /dashboard/1 | 200 | 37 | 12.3 | ... |".
    preg_match_all('/\|\s*(Dashboard|Kanban|Tasks list|Projects|Calendar)\s*\|[^|]*\|\s*\d+\s*\|\s*([\d,]+)\s*\|/', $output, $matches);
    expect($matches[1])->toHaveCount(5);
    foreach ($matches[2] as $queryCount) {
        expect((int) str_replace(',', '', $queryCount))->toBeGreaterThan(0);
    }
});

test('it writes nothing to the database', function () {
    $before = tableCounts();

    Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 3]);

    expect(tableCounts())->toBe($before);
});

test('it can measure as each role, and reports a non-200 as data rather than failing', function () {
    foreach (['management', 'staff', 'client'] as $role) {
        $exit = Artisan::call('perf:baseline', ['--role' => $role, '--runs' => 1]);

        expect($exit)->toBe(0, "perf:baseline should succeed for {$role}");
        expect(Artisan::output())->toContain('Tasks list');
    }
});

test('it fails clearly when the company or the user does not exist', function () {
    expect(Artisan::call('perf:baseline', ['--organization' => 'no-such-co']))->toBe(1);
    expect(Artisan::output())->toContain('perf:seed');

    expect(Artisan::call('perf:baseline', ['--user' => 'nobody@example.com']))->toBe(1);
    expect(Artisan::output())->toContain('No user with email');
});

test('--user overrides --role', function () {
    $email = DB::table('users')->where('email', 'like', '%'.PerformanceSeeder::EMAIL_DOMAIN)
        ->orderBy('id')->value('email');

    expect(Artisan::call('perf:baseline', ['--user' => $email, '--runs' => 1]))->toBe(0);
    expect(Artisan::output())->toContain($email);
});
