<?php

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

/**
 * task #72: the baseline command is what phases 1-10 are judged by, so
 * two bugs in its own measurement had to go first.
 *
 * 1. The query count was multiplied by --runs. DB::listen() cannot be
 *    undone, and the per-run closures all captured the same
 *    function-scoped $count by reference, so run 2 counted every query
 *    twice and run 3 three times. Phase 0's local --runs=3 numbers were
 *    exactly 3x the dev --runs=1 ones.
 * 2. --memory LOWERED a higher limit. Dev's CLI limit is 2048M; the
 *    command set it to the 1G default and then reported that it had
 *    "raised" it.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    Artisan::call('perf:seed', [
        '--tasks' => 10,
        '--comments' => 20,
        '--users' => 6,
        '--projects' => 2,
        '--departments' => 2,
    ]);
});

/** @return array<string, int> page label => query count, parsed from the rendered table */
function baselineQueryCounts(string $output): array
{
    preg_match_all(
        '/\|\s*(Dashboard|Kanban|Tasks list|Projects|Calendar)\s*\|[^|]*\|[^|]*\|\s*([\d,]+)\s*\|/',
        $output,
        $matches,
        PREG_SET_ORDER
    );

    $counts = [];
    foreach ($matches as $match) {
        $counts[trim($match[1])] = (int) str_replace(',', '', $match[2]);
    }

    return $counts;
}

test('the query count does not scale with --runs', function () {
    Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 1]);
    $oneRun = baselineQueryCounts(Artisan::output());

    Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 3]);
    $threeRuns = baselineQueryCounts(Artisan::output());

    expect($oneRun)->not->toBeEmpty();
    expect(array_keys($threeRuns))->toBe(array_keys($oneRun));

    foreach ($oneRun as $page => $count) {
        // Not asserted as exactly equal: --runs=1 reports a COLD request
        // while --runs=3 reports the warm third one, so the warm figure is
        // legitimately a query or two lower. What must never happen again
        // is it being HIGHER — under the old per-run listener it was
        // exactly 3x, which this bound catches with room to spare.
        expect($threeRuns[$page])->toBeLessThanOrEqual(
            $count,
            "{$page}: --runs=3 reported {$threeRuns[$page]} queries but a single cold run reported only {$count}"
        );
    }
});

test('a memory_limit higher than --memory is left alone, with no "raised" warning', function () {
    $original = ini_get('memory_limit');
    ini_set('memory_limit', '2048M');

    try {
        Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 1, '--memory' => '1G']);
        $output = Artisan::output();

        expect(ini_get('memory_limit'))->toBe('2048M');
        expect($output)->not->toContain('was raised');
        // The banner still reports the limit actually in force.
        expect($output)->toContain('2048M');
    } finally {
        ini_set('memory_limit', $original);
    }
});

test('a memory_limit lower than --memory is raised, and the warning says so', function () {
    $original = ini_get('memory_limit');
    ini_set('memory_limit', '256M');

    try {
        Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 1, '--memory' => '1G']);
        $output = Artisan::output();

        expect(ini_get('memory_limit'))->toBe('1G');
        expect($output)->toContain('was raised from 256M to 1G');
    } finally {
        ini_set('memory_limit', $original);
    }
});

test('an unlimited memory_limit (-1) is never lowered', function () {
    $original = ini_get('memory_limit');
    ini_set('memory_limit', '-1');

    try {
        Artisan::call('perf:baseline', ['--role' => 'management', '--runs' => 1, '--memory' => '1G']);

        expect(ini_get('memory_limit'))->toBe('-1');
        expect(Artisan::output())->not->toContain('was raised');
    } finally {
        ini_set('memory_limit', $original);
    }
});
