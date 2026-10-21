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
 * target; PHASE 2 (on-demand row preview) moved the fifth. Every number
 * below was re-measured after each landed:
 *
 *   Page                  phase 0      phase 1     phase 2     target met?
 *   Dashboard             68 /   66    16 /  14    16 /  14    yes - flat, <= 30
 *   Projects              91 /   89    17 /  15    17 /  15    yes - flat, <= 30
 *   Calendar              60 /   60    10 /  10    10 /  10    yes - flat, <= 30
 *   Kanban                153 /  331   21 /  19    21 /  19    yes - flat, <= 30
 *   Kanban (staff)        135 /  223   22 /  20    22 /  20    yes - flat, <= 30
 *   Tasks list            412 / 1220   80 / 198    21 /  19    yes - flat, <= 30
 *   Tasks list (staff)    398 / 1071   83 / 201    24 /  22    yes - flat, <= 30
 *
 * (counts shown as "5 tasks / 20 tasks")
 *
 * Every page is now on the real target, so there are no ratchets left in
 * this file — each test asserts flat AND <= PAGE_QUERY_TARGET, with the
 * measured ceiling kept alongside to stop it drifting back up.
 *
 * Kanban becoming flat is the notable one: its per-card
 * Gate::allows('updateStatus')/Gate::allows('update') calls were the
 * growth, and they were growth only because each one re-queried roles and
 * permissions. Cached, the per-card cost collapses to nothing.
 *
 * Caching visibleOrganizationIds() took another 2-7 queries off every
 * page on top of that: BelongsToOrganization's global scope calls it on
 * every scoped query, so it was being re-answered once per query on
 * pages that run dozens.
 *
 * Deferring the profile's department-grant load took one more off each
 * page, and one per DISTINCT user on the Tasks list (3 here). Grants are
 * only ever asked about the signed-in user, but Task::viewableUsers()
 * builds a profile for every candidate assignee just to ask a permission
 * question, and each of those was paying for grants nothing would read.
 *
 * The Tasks list's own remaining growth was viewableUsers(), called once
 * per row to build the drilldown's @mention list. Phase 2 removed the
 * reason to call it: the drilldown is read-only now and fetched on
 * demand, so there is no mention autocomplete on the list to feed. The
 * preview endpoint has its own constant-cost test — see
 * tests/Feature/Tasks/TaskPreviewTest.php.
 */
const PAGE_QUERY_TARGET = 30;

/** A flat page may wobble by this much without counting as growth. */
const GROWTH_TOLERANCE = 2;

// --- Measured ceilings at 20 tasks, as management ------------------------
// All four now assert the REAL target (flat AND <= PAGE_QUERY_TARGET).
// The ceilings are kept alongside it as the ratchet: <= 30 is the
// contract, <= 19 is what stops it drifting back up to 29.
const DASHBOARD_MAX_QUERIES_20_TASKS = 14;
const PROJECTS_MAX_QUERIES_20_TASKS = 15;
const CALENDAR_MAX_QUERIES_20_TASKS = 10;
const KANBAN_MAX_QUERIES_20_TASKS = 19;
const TASKS_LIST_MAX_QUERIES_20_TASKS = 19;

// --- Measured ceilings at 20 tasks, as staff -----------------------------
// Staff visibility runs through department access instead of the
// management-tier shortcut, so it is a genuinely different query path.
const TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF = 22;
const KANBAN_MAX_QUERIES_20_TASKS_STAFF = 20;

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

test('Tasks list: flat in task count and within the target', function () {
    // Was 412 -> 1220 at phase 0 and still 83 -> 201 after phase 1: the
    // hidden drilldown row rendered the description and the full
    // _subtasks/_comments partials for every task whether anyone opened it
    // or not, and called viewableUsers() (~8 queries) per row to do it.
    // Phase 2 fetches tasks.preview on first open instead, so the list
    // itself is flat — the last of the five pages to get there.
    $counts = measurePageGrowth($this, $this->management, route('tasks.index', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(TASKS_LIST_MAX_QUERIES_20_TASKS);
});

test('Tasks list as STAFF: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->staff, route('tasks.index', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF);
});

test('Kanban as STAFF: flat in task count and within the target', function () {
    $counts = measurePageGrowth($this, $this->staff, route('kanban', $this->org, false));

    expect($counts['large'] - $counts['small'])->toBeLessThanOrEqual(GROWTH_TOLERANCE);
    expect($counts['large'])->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    expect($counts['large'])->toBeLessThanOrEqual(KANBAN_MAX_QUERIES_20_TASKS_STAFF);
});
