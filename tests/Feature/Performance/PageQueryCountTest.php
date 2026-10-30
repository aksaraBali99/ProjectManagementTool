<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KanbanController;
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
 * PHASE 4 (pagination) added exactly one query to the Tasks list, the
 * paginator's count(*): 22 / 20 as management, 25 / 23 as staff. In
 * exchange the page renders 25 rows however many tasks there are; see the
 * 60-task test at the bottom.
 *
 * PHASE 5 (Kanban column cap) runs one grouped count plus one capped query
 * per NON-EMPTY column instead of one query for the whole company. Here
 * every task is Pending, so that is +1: 21 / 20 as management, 22 / 21 as
 * staff. With all four columns in use it is +4, still within the target —
 * see the full-board test at the bottom, which also pins 30 cards per
 * column and a Load more whose cost doesn't grow with the column.
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
// +2 since phase 8 (task #72): on the database cache store each page reads
// the badge colours once per enum (TaskStatus, Priority). Calendar renders
// no badge here (these tasks have no due date), so it is unchanged.
// All four now assert the REAL target (flat AND <= PAGE_QUERY_TARGET).
// The ceilings are kept alongside it as the ratchet: <= 30 is the
// contract, <= 19 is what stops it drifting back up to 29.
// Dashboard: +5 in phase 8. Per section, a limited query instead of one
// load of every task: grouped count, five lists of 10, My Task's count.
const DASHBOARD_MAX_QUERIES_20_TASKS = 21;
const PROJECTS_MAX_QUERIES_20_TASKS = 17;
const CALENDAR_MAX_QUERIES_20_TASKS = 10;
const KANBAN_MAX_QUERIES_20_TASKS = 22;
// Tasks list: +1 since phase 4 (task #72), the page's own count(*) for the
// paginator. It shows one page now, so this stays put however many tasks.
const TASKS_LIST_MAX_QUERIES_20_TASKS = 22;

// --- Measured ceilings at 20 tasks, as staff -----------------------------
// Staff visibility runs through department access instead of the
// management-tier shortcut, so it is a genuinely different query path.
const TASKS_LIST_MAX_QUERIES_20_TASKS_STAFF = 25;
const KANBAN_MAX_QUERIES_20_TASKS_STAFF = 23;

beforeEach(function () {
    // The database cache store, as on dev and production: phpunit.xml's
    // array store made a cache read free here, which is how a cache read
    // per badge (thousands per page) went unseen until task #72 phase 8.
    config(['cache.default' => 'database']);
    // Badge colours already in the store, as on any server past its first
    // page load; each measured request still reads them once per enum.
    TaskStatus::Pending->badgeText();
    Priority::Medium->badgeText();

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
 * countQueries() for one request, starting it as a new request would.
 *
 * One application serves every request in a test, so Cache::memo()'s badge
 * colours would otherwise carry over and the measured request would skip
 * reads a real request pays for. This also drops AccessProfileCache, which
 * FlushAccessProfileCache empties at the start of every request anyway.
 */
function countRequestQueries(callable $request): int
{
    app()->forgetScopedInstances();

    return countQueries($request);
}

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
    $small = countRequestQueries(function () use ($context, $user, $url) {
        $context->actingAs($user)->get($url)->assertOk();
    });

    makeMeasuredTasks($context, 15);
    $large = countRequestQueries(function () use ($context, $user, $url) {
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

test('Tasks list: a 60-task company renders exactly one page of 25 rows', function () {
    // task #72 phase 4: the list used to render every visible task.
    // Plain tasks are enough here: this is about row count, not row cost.
    for ($i = 0; $i < 60; $i++) {
        Task::create([
            'organization_id' => $this->org->id,
            'project_id' => $this->project->id,
            'department_id' => $this->dept->id,
            'title' => 'Paged task '.$i,
            'priority' => 'medium',
            'status' => 'pending',
        ]);
    }

    $queries = countRequestQueries(function () use (&$html) {
        $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org, false))->assertOk()->getContent();
    });

    expect(substr_count($html, 'data-drilldown-toggle='))->toBe(25);
    // Against the target, not the 20-task ceiling: this is the test's first
    // request, which pays the one-off cache warm-up the measured ceilings
    // above (taken on a second request) don't.
    expect($queries)->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
});

/** Plain tasks (no subtasks/comments): these tests are about card count, not card cost. */
function makePlainKanbanTasks(object $context, int $count, string $status): void
{
    for ($i = 0; $i < $count; $i++) {
        Task::create([
            'organization_id' => $context->org->id,
            'project_id' => $context->project->id,
            'department_id' => $context->dept->id,
            'assignee_id' => $context->staff->id,
            'title' => 'Board task '.$status.' '.$i,
            'priority' => ['high', 'medium', 'low'][$i % 3],
            'status' => $status,
        ]);
    }
}

test('Kanban: with every column over 30, the board shows 30 cards per column within the target', function () {
    // task #72 phase 5: the board used to render every card in the company.
    foreach (TaskStatus::cases() as $status) {
        makePlainKanbanTasks($this, 40, $status->value);
    }

    foreach ([$this->management, $this->staff] as $user) {
        $html = null;
        $queries = countRequestQueries(function () use ($user, &$html) {
            $html = $this->actingAs($user)->get(route('kanban', $this->org, false))->assertOk()->getContent();
        });

        expect(substr_count($html, 'class="kanban-card '))->toBe(4 * KanbanController::COLUMN_BATCH);
        expect($queries)->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    }
});

test('Kanban Load more: the same number of queries whether the column holds 30 or 300 tasks', function () {
    $url = route('kanban.column', [$this->org, 'pending'], false).'?offset=0';

    $measure = function () use ($url) {
        return countRequestQueries(function () use ($url) {
            $this->actingAs($this->management)->getJson($url)->assertOk();
        });
    };

    makePlainKanbanTasks($this, 30, 'pending');
    // Warm-up request: the first request in a test pays one-off cache
    // misses the measured ones don't.
    $measure();
    $small = $measure();

    makePlainKanbanTasks($this, 270, 'pending');
    $large = $measure();

    expect($large)->toBe($small);
    expect($large)->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
});

test('Dashboard: with every section over 10, each shows 10 within the target, for every role', function () {
    // task #72 phase 8: the Dashboard used to load and render every task.
    // 15 per status x priority: 60 High (30 of them Active), 60 Medium,
    // 60 Low, all assigned to the staff member so My Task is full too.
    foreach (['high', 'medium', 'low'] as $priority) {
        foreach (TaskStatus::cases() as $status) {
            for ($i = 0; $i < 15; $i++) {
                Task::create([
                    'organization_id' => $this->org->id,
                    'project_id' => $this->project->id,
                    'department_id' => $this->dept->id,
                    'assignee_id' => $this->staff->id,
                    'title' => "Dashboard task {$priority} {$status->value} {$i}",
                    'priority' => $priority,
                    'status' => $status->value,
                ]);
            }
        }
    }

    foreach ([$this->owner, $this->management, $this->staff] as $user) {
        $response = null;
        $queries = countRequestQueries(function () use ($user, &$response) {
            $response = $this->actingAs($user)->get(route('dashboard', $this->org, false))->assertOk();
        });

        $sections = $response->viewData('priorityGroups')->values()
            ->push($response->viewData('activeTasks'), $response->viewData('myTasks'));
        foreach ($sections as $section) {
            expect($section)->toHaveCount(DashboardController::SECTION_LIMIT);
        }
        // Every rendered task card links to its task page with a
        // Dashboard return label: 5 sections x 10, never the company.
        expect(substr_count($response->getContent(), 'return_label=Dashboard'))->toBe(5 * DashboardController::SECTION_LIMIT);
        expect($queries)->toBeLessThanOrEqual(PAGE_QUERY_TARGET);
    }
});
