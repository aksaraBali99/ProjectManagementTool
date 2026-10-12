<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

/**
 * task #72 phase 0: a ratchet, not a target.
 *
 * Several pages currently run one or more queries PER TASK, so their cost
 * grows with the data. This file pins today's numbers so they cannot get
 * worse while phases 1-10 bring them down. Every constant below was
 * MEASURED, not estimated, and each carries the phase expected to tighten
 * it.
 *
 * IMPORTANT: these are SQLite in-memory counts from the test environment,
 * because that is what CI asserts against. They are NOT comparable with
 * the MySQL numbers `php artisan perf:baseline` prints — different driver,
 * and the baseline command measures a 2,000-task company rather than 20.
 * Keep the two sets of numbers apart when recording results.
 *
 * Two different properties are asserted, because the pages differ:
 *
 *   - GROWTH: does the count rise with the number of tasks? Dashboard,
 *     Projects and Calendar are already flat, so they get a real
 *     behavioural assertion that they stay flat. Tasks list and Kanban
 *     are not, and the test records that explicitly so the day they
 *     become flat is a deliberate change, not an accident.
 *   - CEILING: today's absolute count, as a ratchet.
 *
 * On the <= 30 target: no page meets it yet, not even the flat ones
 * (Dashboard 66, Projects 89, Calendar 60 at 20 tasks). That cost is
 * per-REQUEST, not per-task — overwhelmingly User::hasPermission(), which
 * runs 3 uncached queries on every call and is called many times per
 * render. Phase 1 (permission cache) is what brings the flat pages under
 * the target; phases 1-2 then flatten Tasks list and Kanban. PAGE_QUERY_
 * TARGET is recorded here so the goal is visible, but it is deliberately
 * not asserted yet — doing so would simply fail on day one.
 */
const PAGE_QUERY_TARGET = 30;

/** A flat page may wobble by this much without counting as growth. */
const GROWTH_TOLERANCE = 2;

// --- Measured ceilings at 20 tasks, as management ------------------------
// Flat today (growth asserted); phase 1's permission cache should take
// these three under PAGE_QUERY_TARGET.
const DASHBOARD_MAX_QUERIES_20_TASKS = 66;
const PROJECTS_MAX_QUERIES_20_TASKS = 89;
const CALENDAR_MAX_QUERIES_20_TASKS = 60;

// Still grow per task; phases 1-2 tighten these to <= 30 and flat.
const KANBAN_MAX_QUERIES_20_TASKS = 331;
const TASKS_LIST_MAX_QUERIES_20_TASKS = 1220;

// --- Measured ceilings at 20 tasks, as staff -----------------------------
// Staff visibility runs through department access instead of the
// management-tier shortcut, so it is a genuinely different query path.
const TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF = 1071;
const KANBAN_MAX_QUERIES_20_TASKS_STAFF = 223;

beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Perf Org', 'slug' => 'perf-org', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $this->staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);
    AccessPermission::create([
        'user_id' => $this->staff->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);
    $this->project->staff()->attach($this->staff->id);
});

/**
 * Tasks with the shape that actually costs something on these pages: 2
 * subtasks and 3 comments each, one comment carrying a mention and a
 * reaction (the relations tasks/index.blade.php eager-loads per row).
 */
function makeMeasuredTasks(object $context, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $task = Task::create([
            'organization_id' => $context->org->id,
            'project_id' => $context->project->id,
            'department_id' => $context->dept->id,
            'assignee_id' => $context->staff->id,
            'title' => 'Measured task '.uniqid(),
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        for ($s = 0; $s < 2; $s++) {
            Subtask::create(['task_id' => $task->id, 'title' => 'Sub '.$s, 'assignee_id' => $context->staff->id]);
        }

        foreach (range(1, 3) as $c) {
            $comment = Comment::create([
                'task_id' => $task->id,
                'user_id' => $context->management->id,
                'body' => '<p>Comment '.$c.'</p>',
            ]);

            if ($c === 1) {
                $comment->mentionedUsers()->attach($context->staff->id);
                $comment->reactions()->create(['user_id' => $context->staff->id, 'emoji' => '👍']);
            }
        }
    }
}

/**
 * Measures a page at 5 tasks, adds 15 more, measures again. Both requests
 * must return 200 — a page that started 500ing would otherwise "improve"
 * its query count dramatically.
 *
 * @return array{small:int, large:int}
 */
function measurePageGrowth(object $context, User $user, string $url): array
{
    makeMeasuredTasks($context, 5);
    $small = countQueries(function () use ($context, $user, $url) {
        $context->actingAs($user)->get($url)->assertOk();
    });

    makeMeasuredTasks($context, 15);
    $large = countQueries(function () use ($context, $user, $url) {
        $context->actingAs($user)->get($url)->assertOk();
    });

    return ['small' => $small, 'large' => $large];
}

test('Dashboard: flat in task count, held at today s ceiling', function () {
    $counts = measurePageGrowth($this, $this->management, route('dashboard', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(DASHBOARD_MAX_QUERIES_20_TASKS);
});

test('Projects: flat in task count, held at today s ceiling', function () {
    $counts = measurePageGrowth($this, $this->management, route('projects.index', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PROJECTS_MAX_QUERIES_20_TASKS);
});

test('Calendar: flat in task count, held at today s ceiling', function () {
    $counts = measurePageGrowth($this, $this->management, route('calendar', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(CALENDAR_MAX_QUERIES_20_TASKS);
});

test('Kanban: held at today s ceiling, and still grows per card', function () {
    $counts = measurePageGrowth($this, $this->management, route('kanban', $this->org, false));

    expect($counts['large'])->toBeLessThanOrEqual(KANBAN_MAX_QUERIES_20_TASKS);
    // Records the problem phases 1-2 will fix. When this assertion starts
    // failing, Kanban has gone flat — swap it for the growth + target
    // assertions the three pages above use.
    expect($counts['large'])->toBeGreaterThan($counts['small']);
});

test('Tasks list: held at today s ceiling, and still grows per row', function () {
    $counts = measurePageGrowth($this, $this->management, route('tasks.index', $this->org, false));

    expect($counts['large'])->toBeLessThanOrEqual(TASKS_LIST_MAX_QUERIES_20_TASKS);
    expect($counts['large'])->toBeGreaterThan($counts['small']);
});

test('Tasks list as STAFF: held at today s ceiling, and still grows per row', function () {
    $counts = measurePageGrowth($this, $this->staff, route('tasks.index', $this->org, false));

    expect($counts['large'])->toBeLessThanOrEqual(TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF);
    expect($counts['large'])->toBeGreaterThan($counts['small']);
});

test('Kanban as STAFF: held at today s ceiling, and still grows per card', function () {
    $counts = measurePageGrowth($this, $this->staff, route('kanban', $this->org, false));

    expect($counts['large'])->toBeLessThanOrEqual(KANBAN_MAX_QUERIES_20_TASKS_STAFF);
    expect($counts['large'])->toBeGreaterThan($counts['small']);
});
