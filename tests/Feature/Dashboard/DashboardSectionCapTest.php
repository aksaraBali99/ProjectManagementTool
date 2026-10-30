<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\DashboardController;
use App\Models\Department;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;

/**
 * task #72 phase 8: each Dashboard section shows its first 10 tasks (soonest
 * due, no due date last, then title, then id), the header shows the full
 * count, and a link opens the rest in the Tasks list.
 *
 * The expected sections are built here in PHP the way the Dashboard built
 * them before phase 8 — load every visible task in the window, filter per
 * section — then sorted and capped, so the SQL version is checked against
 * the rule it replaced.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);
});

function capDashboard(object $context, User $user, array $query = []): TestResponse
{
    app()->forgetScopedInstances();

    return $context->actingAs($user)->get(route('dashboard', [$context->org] + $query))->assertOk();
}

/**
 * @param  Collection<int, Task>  $tasks
 * @return array<int, int> ids in the Dashboard's order
 */
function capSorted(Collection $tasks): array
{
    return $tasks->sort(fn (Task $a, Task $b) => [$a->due_date === null, $a->due_date?->format('Y-m-d'), mb_strtolower($a->title), $a->id]
        <=> [$b->due_date === null, $b->due_date?->format('Y-m-d'), mb_strtolower($b->title), $b->id])
        ->pluck('id')->values()->all();
}

/**
 * Every section, uncapped, by the pre-phase-8 PHP rules.
 *
 * @param  array<int, int>  $selectedStaffIds
 * @return array<string, array<int, int>>
 */
function capExpectedSections(object $context, User $user, array $selectedStaffIds = []): array
{
    $context->actingAs($user);
    app()->forgetScopedInstances();
    $organization = $context->org->fresh();

    $tasks = Task::visibleTo($user, $organization->id)
        ->openOrCompletedWithin($organization->completedWindowStart())
        ->with('subtasks')
        ->get();

    $sections = [
        'active' => capSorted($tasks->filter(fn (Task $task) => $task->priority === Priority::High
            && in_array($task->status, [TaskStatus::InProgress, TaskStatus::InReview], true))),
    ];
    foreach (Priority::cases() as $priority) {
        $sections[$priority->value] = capSorted($tasks->where('priority', $priority));
    }

    if ($user->isSuperAdmin() || $user->isOwner()) {
        $sections['my'] = capSorted(empty($selectedStaffIds) ? $tasks : $tasks->whereIn('assignee_id', $selectedStaffIds));
    } elseif ($user->isManagementInOrg($organization->id)) {
        $staffIds = OrgMember::where('organization_id', $organization->id)
            ->where('role_id', Role::where('slug', 'staff')->value('id'))
            ->pluck('user_id');
        $sections['my'] = capSorted($tasks->whereIn('assignee_id', $staffIds));
    } elseif ($user->isStaffInOrg($organization->id)) {
        $departments = $user->allowedActiveDepartmentIds($organization->id);
        $sections['my'] = capSorted($tasks->filter(fn (Task $task) => $departments->contains($task->department_id)
            && ($task->assignee_id === $user->id || $task->subtasks->contains('assignee_id', $user->id))));
    }

    return $sections;
}

/**
 * Asserts each rendered section is the first 10 of the expected one, and
 * each header count the full expected count.
 *
 * @param  array<string, array<int, int>>  $expected
 */
function assertCappedSections(TestResponse $response, array $expected): void
{
    $limit = DashboardController::SECTION_LIMIT;
    $counts = $response->viewData('sectionCounts');

    expect($response->viewData('activeTasks')->pluck('id')->all())->toBe(array_slice($expected['active'], 0, $limit))
        ->and($counts['active'])->toBe(count($expected['active']));

    foreach (Priority::cases() as $priority) {
        expect($response->viewData('priorityGroups')[$priority->value]->pluck('id')->all())->toBe(array_slice($expected[$priority->value], 0, $limit))
            ->and($counts[$priority->value])->toBe(count($expected[$priority->value]));
    }

    if (array_key_exists('my', $expected)) {
        expect($response->viewData('myTasks')->pluck('id')->all())->toBe(array_slice($expected['my'], 0, $limit))
            ->and($counts['my'])->toBe(count($expected['my']));
    } else {
        expect($response->viewData('myTaskMode'))->toBe('none')
            ->and($response->viewData('myTasks'))->toHaveCount(0);
    }
}

/**
 * A mixed company, every section over 10: two departments (the staff
 * member is granted only the first), a second project the client isn't
 * on, a second staff member, tasks assigned to management and to the
 * owner, unassigned ones, subtask-only assignments, undated tasks, title
 * and due-date ties, and completed tasks either side of the window.
 */
function capMixedCompany(object $context): void
{
    $context->otherDept = Department::create(['organization_id' => $context->org->id, 'name' => 'Sales', 'color' => '#000000']);
    $context->otherProject = Project::create(['organization_id' => $context->org->id, 'name' => 'Project B', 'description' => 'd']);
    $context->staff2 = User::factory()->create();
    OrgMember::create(['organization_id' => $context->org->id, 'user_id' => $context->staff2->id, 'role_id' => Role::where('slug', 'staff')->value('id')]);

    $assignees = [$context->staff->id, $context->staff2->id, $context->management->id, $context->owner->id, null];
    $dues = [null, '2026-11-02', '2026-11-05', '2026-11-05', null, '2026-10-28', '2026-12-01'];
    $titles = ['Same title', 'alpha', 'Beta', 'same title', 'Gamma'];
    $statuses = ['pending', 'in_progress', 'in_review', 'completed'];

    for ($i = 0; $i < 72; $i++) {
        $task = makeWindowTask($context, $statuses[$i % 4], null, [
            'project_id' => $i % 6 === 0 ? $context->otherProject->id : $context->project->id,
            'department_id' => $i % 5 === 0 ? $context->otherDept->id : $context->dept->id,
            'assignee_id' => $assignees[$i % 5],
            'priority' => ['high', 'medium', 'low'][$i % 3],
            'title' => $titles[$i % 5],
            'due_date' => $dues[$i % 7],
        ]);

        if ($i % 9 === 4) {
            Subtask::create(['task_id' => $task->id, 'title' => 'Sub of '.$task->id, 'assignee_id' => $context->staff->id]);
        }
    }

    // Completed before the 14-day window: out of every section and count.
    $context->oldCompleted = collect(range(1, 6))->map(fn ($i) => makeWindowTask($context, 'completed', '2026-09-01 08:00:00', [
        'priority' => ['high', 'medium', 'low'][$i % 3],
        'assignee_id' => $context->staff->id,
        'due_date' => '2026-10-01',
    ])->id)->all();
}

test('a section with more than 10 tasks shows the first 10: soonest due, undated last, then title, then id', function () {
    // All High and In progress, so Active, High and My Task hold the same 14.
    // Created in this order on purpose: ids don't follow the expected order.
    $make = fn (string $title, ?string $due) => makeWindowTask($this, 'in_progress', null, [
        'priority' => 'high', 'title' => $title, 'due_date' => $due, 'assignee_id' => $this->staff->id,
    ])->id;

    $undated = [$make('Undated A', null), $make('Undated B', null), $make('Undated C', null)];
    $sameTitleLater = $make('Same title', '2026-11-06');
    $beta = $make('Beta', '2026-11-05');
    $sameTitleEarlier = $make('Same title', '2026-11-06');
    $alpha = $make('alpha', '2026-11-05');
    $rest = collect(['2026-11-12', '2026-11-01', '2026-11-09', '2026-11-03', '2026-11-08', '2026-11-04', '2026-11-11'])
        ->mapWithKeys(fn ($due) => [$due => $make('Task due '.$due, $due)]);

    $expected = [
        $rest['2026-11-01'], $rest['2026-11-03'], $rest['2026-11-04'],
        $alpha, $beta,                            // same day: title, case-insensitive
        $sameTitleLater, $sameTitleEarlier,       // same day and title: id
        $rest['2026-11-08'], $rest['2026-11-09'], $rest['2026-11-11'],
    ];
    expect($sameTitleLater)->toBeLessThan($sameTitleEarlier);

    $response = capDashboard($this, $this->owner);

    foreach ([$response->viewData('activeTasks'), $response->viewData('priorityGroups')['high'], $response->viewData('myTasks')] as $section) {
        expect($section->pluck('id')->all())->toBe($expected);
    }
    // Undated tasks are crowded out by dated ones: they come last now.
    expect(array_intersect($undated, $response->viewData('myTasks')->pluck('id')->all()))->toBe([])
        ->and($response->viewData('sectionCounts'))->toMatchArray(['active' => 14, 'high' => 14, 'my' => 14]);
});

test('a section with 10 or fewer tasks shows them all, undated last', function () {
    $undated = makeWindowTask($this, 'pending', null, ['priority' => 'low', 'title' => 'Aardvark'])->id;
    $later = makeWindowTask($this, 'pending', null, ['priority' => 'low', 'due_date' => '2026-11-20'])->id;
    $sooner = makeWindowTask($this, 'pending', null, ['priority' => 'low', 'due_date' => '2026-11-02'])->id;

    $response = capDashboard($this, $this->management);

    expect($response->viewData('priorityGroups')['low']->pluck('id')->all())->toBe([$sooner, $later, $undated])
        ->and($response->viewData('sectionCounts')['low'])->toBe(3)
        ->and($response->getContent())->toContain('View all 3 in Tasks list');
});

test('every section matches the pre-phase-8 rules, capped, for each role', function (string $who, array $query) {
    capMixedCompany($this);
    $user = $this->{$who};
    $selected = array_map(fn ($name) => $this->{$name}->id, $query);

    $response = capDashboard($this, $user, $selected ? ['staff' => $selected] : []);
    $expected = capExpectedSections($this, $user, $selected);

    // The fixture really fills the sections it is meant to.
    expect(count($expected['high']))->toBeGreaterThan(DashboardController::SECTION_LIMIT);
    assertCappedSections($response, $expected);
})->with([
    'owner, nobody ticked' => ['owner', []],
    'owner, one ticked' => ['owner', ['staff2']],
    'owner, several ticked' => ['owner', ['staff', 'management']],
    'management' => ['management', []],
    'staff' => ['staff', []],
    'client' => ['client', []],
]);

test('staff My Task: own tasks and subtask-only tasks in a granted department, nothing outside it', function () {
    $otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#000000']);

    $own = makeWindowTask($this, 'pending', null, ['assignee_id' => $this->staff->id, 'title' => 'Own task']);
    $viaSubtask = makeWindowTask($this, 'pending', null, ['assignee_id' => $this->management->id, 'title' => 'Via subtask']);
    Subtask::create(['task_id' => $viaSubtask->id, 'title' => 'Someone else\'s step', 'assignee_id' => $this->management->id]);
    Subtask::create(['task_id' => $viaSubtask->id, 'title' => 'My step', 'assignee_id' => $this->staff->id]);
    // Assigned to them, so visible to them, but not in a department they can access.
    $outside = makeWindowTask($this, 'pending', null, ['assignee_id' => $this->staff->id, 'department_id' => $otherDept->id, 'title' => 'Outside task']);
    $notMine = makeWindowTask($this, 'pending', null, ['assignee_id' => $this->management->id, 'title' => 'Not mine']);

    $response = capDashboard($this, $this->staff);
    $myTasks = $response->viewData('myTasks');

    expect($myTasks->pluck('id')->sort()->values()->all())->toBe([$own->id, $viaSubtask->id])
        ->and($response->viewData('sectionCounts')['my'])->toBe(2)
        ->and($response->viewData('priorityGroups')['medium']->pluck('id'))->toContain($outside->id, $notMine->id);
    $response->assertSee('Your subtask: My step');

    // Only their own subtasks are loaded on this page (the one reader is
    // the "Your subtask" line).
    expect($myTasks->firstWhere('id', $viaSubtask->id)->subtasks->pluck('title')->all())->toBe(['My step']);
});

test('old completed tasks stay out of every section and every count', function (string $who) {
    capMixedCompany($this);
    $response = capDashboard($this, $this->{$who});

    $shown = $response->viewData('priorityGroups')->flatten(1)->pluck('id')
        ->merge($response->viewData('activeTasks')->pluck('id'))
        ->merge($response->viewData('myTasks')->pluck('id'));
    expect($shown->intersect($this->oldCompleted)->all())->toBe([]);

    assertCappedSections($response, capExpectedSections($this, $this->{$who}));
})->with(['owner', 'management', 'staff', 'client']);

test('no task appears that Task::visibleTo() would not return', function (string $who) {
    capMixedCompany($this);
    $user = $this->{$who};
    $response = capDashboard($this, $user);

    $visible = Task::visibleTo($user, $this->org->id)->pluck('id');
    $shown = $response->viewData('priorityGroups')->flatten(1)->pluck('id')
        ->merge($response->viewData('activeTasks')->pluck('id'))
        ->merge($response->viewData('myTasks')->pluck('id'));

    expect($shown)->not->toBeEmpty()
        ->and($shown->diff($visible)->all())->toBe([]);
})->with(['management', 'staff', 'client']);

test('each priority group links to the Tasks list showing exactly its count', function (string $who) {
    capMixedCompany($this);
    $user = $this->{$who};
    $response = capDashboard($this, $user);
    $html = $response->getContent();

    foreach (Priority::cases() as $priority) {
        $count = $response->viewData('sectionCounts')[$priority->value];
        $link = $response->viewData('sectionLinks')[$priority->value];

        expect($link)->toBe([
            'url' => route('tasks.index', [$this->org, 'priority' => $priority->value]),
            'label' => "View all {$count} in Tasks list",
        ]);
        expect($html)->toContain('href="'.e($link['url']).'"')
            ->and($html)->toContain($link['label']);

        $listIds = taskListIdsAcrossPages($user, $this->org->id, ['priority' => $priority->value]);
        expect($listIds)->toHaveCount($count)
            ->and(collect($listIds)->sort()->values()->all())->toBe(collect(capExpectedSections($this, $user)[$priority->value])->sort()->values()->all());
    }
})->with(['management', 'staff', 'client']);

test('Active links to every High task in the Tasks list, without a number', function () {
    capMixedCompany($this);
    $response = capDashboard($this, $this->management);

    expect($response->viewData('sectionLinks')['active'])->toBe([
        'url' => route('tasks.index', [$this->org, 'priority' => 'high']),
        'label' => 'Open in Tasks list',
    ]);
    expect($response->getContent())->toContain('href="'.e(route('tasks.index', [$this->org, 'priority' => 'high'])).'"');
});

test('an owner\'s My Task link with one person ticked opens exactly that many tasks', function () {
    capMixedCompany($this);
    $response = capDashboard($this, $this->owner, ['staff' => [$this->staff2->id]]);
    $count = $response->viewData('sectionCounts')['my'];

    expect($response->viewData('myTasksLink')['label'])->toBe("View all {$count} in Tasks list")
        ->and(taskListIdsAcrossPages($this->owner, $this->org->id, ['assignee_id' => $this->staff2->id]))->toHaveCount($count);
});

test('an empty section still shows its link and its empty message', function () {
    $response = capDashboard($this, $this->management);

    expect($response->viewData('sectionCounts'))->toMatchArray(['active' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'my' => 0]);
    $response->assertSee('No active high-priority tasks')
        ->assertSee('View all 0 in Tasks list')
        ->assertSee('Open in Tasks list');
});
