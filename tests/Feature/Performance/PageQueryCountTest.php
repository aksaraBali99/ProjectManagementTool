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
 * task #72: how many queries each heavy page costs, and whether that cost
 * grows with the number of tasks.
 *
 * Every constant below was MEASURED, not estimated. Each page asserts one
 * of two things:
 *
 *   - THE TARGET: flat in task count AND within PAGE_QUERY_TARGET. Used
 *     for every page that has reached it.
 *   - A RATCHET: today's measured ceiling, plus an explicit assertion
 *     that the page DOES still grow — so the day it stops growing is a
 *     deliberate change rather than something nobody noticed.
 *
 * IMPORTANT: these are SQLite in-memory counts from the test environment,
 * because that is what CI asserts against. They are NOT comparable with
 * the MySQL numbers `php artisan perf:baseline` prints — different driver,
 * and the baseline command measures a 2,000-task company rather than 20.
 * Keep the two sets of numbers apart when recording results.
 *
 * PHASE 1 (permission cache) moved four of the five pages onto the real
 * target. Every number below was re-measured after it landed:
 *
 *   Page                  phase 0      phase 1     target met?
 *   Dashboard             68 /   66    21 /  19    yes - flat, <= 30
 *   Projects              91 /   89    23 /  21    yes - flat, <= 30
 *   Calendar              60 /   60    14 /  14    yes - flat, <= 30
 *   Kanban                153 /  331   29 /  27    yes - now FLAT too
 *   Kanban (staff)        135 /  223   32 /  30    yes - now FLAT too
 *   Tasks list            412 / 1220  106 / 254    no  - phase 2
 *   Tasks list (staff)    398 / 1071  114 / 262    no  - phase 2
 *
 * (counts shown as "5 tasks / 20 tasks")
 *
 * Kanban becoming flat is the notable one: its per-card
 * Gate::allows('updateStatus')/Gate::allows('update') calls were the
 * growth, and they were growth only because each one re-queried roles and
 * permissions. Cached, the per-card cost collapses to nothing.
 *
 * The Tasks list still grows, and will until phase 2: its per-row
 * $task->viewableUsers() genuinely loads different data per row, which no
 * amount of permission caching fixes.
 */
const PAGE_QUERY_TARGET = 30;

/** A flat page may wobble by this much without counting as growth. */
const GROWTH_TOLERANCE = 2;

// --- Measured ceilings at 20 tasks, as management ------------------------
// All four now assert the REAL target (flat AND <= PAGE_QUERY_TARGET).
// The ceilings are kept alongside it as the ratchet: <= 30 is the
// contract, <= 19 is what stops it drifting back up to 29.
const DASHBOARD_MAX_QUERIES_20_TASKS = 19;
const PROJECTS_MAX_QUERIES_20_TASKS = 21;
const CALENDAR_MAX_QUERIES_20_TASKS = 14;
const KANBAN_MAX_QUERIES_20_TASKS = 27;

// Still grows per row; phase 2 (viewableUsers) tightens this to <= 30.
const TASKS_LIST_MAX_QUERIES_20_TASKS = 254;

// --- Measured ceilings at 20 tasks, as staff -----------------------------
// Staff visibility runs through department access instead of the
// management-tier shortcut, so it is a genuinely different query path.
const TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF = 262;
const KANBAN_MAX_QUERIES_20_TASKS_STAFF = 30;

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

test('Dashboard: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->management, route('dashboard', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(DASHBOARD_MAX_QUERIES_20_TASKS);
});

test('Projects: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->management, route('projects.index', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(PROJECTS_MAX_QUERIES_20_TASKS);
});

test('Calendar: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->management, route('calendar', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(CALENDAR_MAX_QUERIES_20_TASKS);
});

test('Kanban: flat in task count and within the target', function () {
    // Was 153 -> 331 before phase 1: the per-card Gate::allows() calls
    // each re-queried roles and permissions, so the cost scaled with the
    // number of cards. Cached, it is flat.
    $counts = measurePageGrowth($this, $this->management, route('kanban', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(KANBAN_MAX_QUERIES_20_TASKS);
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

test('Kanban as STAFF: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->staff, route('kanban', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(KANBAN_MAX_QUERIES_20_TASKS_STAFF);
});
