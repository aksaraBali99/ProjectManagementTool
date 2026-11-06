<?php

use App\Http\Controllers\ProjectManagementController;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * task #72 phase 9: a project's drilldown on the Projects page is fetched
 * from projects.tasks when its row is first opened: the first 25 tasks
 * (due soonest, no due date last, then title, then id), with a link to the
 * Tasks list filtered by the project. Same access and visibility as the
 * page.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);
});

/**
 * The drilldown's order, built in PHP from the tasks themselves.
 *
 * @param  Collection<int, Task>  $tasks
 * @return array<int, int>
 */
function drilldownSorted(Collection $tasks): array
{
    return $tasks->sort(fn (Task $a, Task $b) => [$a->due_date === null, $a->due_date?->format('Y-m-d'), strtolower($a->title), $a->id]
        <=> [$b->due_date === null, $b->due_date?->format('Y-m-d'), strtolower($b->title), $b->id])
        ->pluck('id')->values()->all();
}

/** Task ids $user can see on this fixture's project, within the window. */
function drilldownExpectedIds(object $context, User $user): array
{
    return drilldownSorted(Task::visibleTo($user, $context->org->id)
        ->where('project_id', $context->project->id)
        ->openOrCompletedWithin($context->org->completedWindowStart())
        ->get());
}

test('a project with more than 25 tasks shows the first 25: soonest due, undated last, then title, then id', function () {
    // 12 dated across 4 days, 3 of them on one day with titles that need
    // the case-insensitive title and the id tie-breaks.
    foreach (range(1, 9) as $i) {
        makeWindowTask($this, 'pending', null, ['title' => 'Dated '.$i, 'due_date' => '2026-11-'.str_pad((string) (($i % 4) + 1), 2, '0', STR_PAD_LEFT)]);
    }
    makeWindowTask($this, 'pending', null, ['title' => 'beta', 'due_date' => '2026-11-03']);
    makeWindowTask($this, 'pending', null, ['title' => 'Alpha', 'due_date' => '2026-11-03']);
    makeWindowTask($this, 'pending', null, ['title' => 'alpha', 'due_date' => '2026-11-03']);
    // 20 undated, two sharing a title.
    foreach (range(1, 18) as $i) {
        makeWindowTask($this, 'pending', null, ['title' => 'Undated '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
    }
    makeWindowTask($this, 'pending', null, ['title' => 'Same']);
    makeWindowTask($this, 'pending', null, ['title' => 'Same']);
    // An old completed task, outside the window.
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00', ['due_date' => '2026-10-01']);

    $expected = drilldownExpectedIds($this, $this->management);
    expect($expected)->toHaveCount(32);

    $response = projectDrilldown($this->management, $this->project);

    expect($response->viewData('tasks')->pluck('id')->all())->toBe(array_slice($expected, 0, ProjectManagementController::DRILLDOWN_LIMIT));

    // The rendered order, not just the view data.
    $titles = $response->viewData('tasks')->pluck('title')->all();
    $offset = 0;
    foreach ($titles as $title) {
        $position = strpos($response->getContent(), '>'.$title.'</a>', $offset);
        expect($position)->not->toBeFalse();
        $offset = $position;
    }
});

test('a project with 25 or fewer shows them all, undated last, linked as "Open in Tasks list"', function () {
    $undated = makeWindowTask($this, 'pending', null, ['title' => 'Aardvark'])->id;
    $later = makeWindowTask($this, 'pending', null, ['due_date' => '2026-11-20'])->id;
    $sooner = makeWindowTask($this, 'pending', null, ['due_date' => '2026-11-02'])->id;

    $response = projectDrilldown($this->management, $this->project);

    expect($response->viewData('tasks')->pluck('id')->all())->toBe([$sooner, $later, $undated])
        ->and($response->viewData('tasksLink'))->toBe([
            'url' => route('tasks.index', [$this->org, 'project_id' => $this->project->id]),
            'label' => 'Open in Tasks list',
        ])
        ->and($response->getContent())->not->toContain('View all');
});

test('over 25, the link reads "View all N" and the Tasks list shows exactly N, in both modes', function (string $who) {
    $user = $this->{$who};
    foreach (range(1, 27) as $i) {
        makeWindowTask($this, 'pending', null, ['assignee_id' => $this->staff->id]);
    }
    makeWindowTask($this, 'completed', '2026-10-28 08:00:00', ['assignee_id' => $this->staff->id]);
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00', ['assignee_id' => $this->staff->id]);
    makeWindowTask($this, 'completed', '2026-02-01 00:00:00', ['assignee_id' => $this->staff->id]);

    // Recent mode: 28 shown.
    $response = projectDrilldown($user, $this->project);
    $link = $response->viewData('tasksLink');
    expect($link)->toBe([
        'url' => route('tasks.index', [$this->org, 'project_id' => $this->project->id]),
        'label' => 'View all 28 in Tasks list',
    ]);
    expect($response->getContent())->toContain('href="'.e($link['url']).'"')->toContain('View all 28 in Tasks list');
    expect(taskListIdsAcrossPages($user, $this->org->id, ['project_id' => $this->project->id]))->toHaveCount(28);

    // Show all mode: every one of the 30.
    $response = projectDrilldown($user, $this->project, ['completed' => 'all']);
    expect($response->viewData('tasks'))->toHaveCount(25)
        ->and($response->viewData('tasksLink'))->toBe([
            'url' => route('tasks.index', [$this->org, 'project_id' => $this->project->id, 'completed' => 'all']),
            'label' => 'View all 30 in Tasks list',
        ]);
    expect(taskListIdsAcrossPages($user, $this->org->id, ['project_id' => $this->project->id, 'completed' => 'all']))->toHaveCount(30);
})->with(['management', 'staff', 'client']);

test('completed=all lists old completed tasks too, in the same order', function () {
    $old = makeWindowTask($this, 'completed', '2026-01-01 00:00:00', ['due_date' => '2026-01-01']);
    $open = makeWindowTask($this, 'pending', null, ['due_date' => '2026-11-05']);

    expect(projectDrilldownIds($this->management, $this->project))->toBe([$open->id])
        ->and(projectDrilldownIds($this->management, $this->project, ['completed' => 'all']))->toBe([$old->id, $open->id]);
});

test('only old completed tasks: "No recent tasks", linking to them with completed=all', function () {
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00');

    $response = projectDrilldown($this->management, $this->project);

    $response->assertSee('No recent tasks')->assertDontSee('No tasks on this project yet.');
    expect($response->viewData('tasksLink'))->toBe([
        'url' => route('tasks.index', [$this->org, 'project_id' => $this->project->id, 'completed' => 'all']),
        'label' => 'Open in Tasks list',
    ]);
});

test('no visible tasks at all: "No tasks on this project yet." and no link', function (array $query) {
    // A task the staff member can't see (another department) changes nothing.
    $hidden = Department::create(['organization_id' => $this->org->id, 'name' => 'Finance', 'color' => '#000000']);
    makeWindowTask($this, 'pending', null, ['department_id' => $hidden->id]);

    $response = projectDrilldown($this->staff, $this->project, $query);

    $response->assertSee('No tasks on this project yet.')->assertDontSee('No recent tasks')->assertDontSee('in Tasks list');
    expect($response->viewData('tasksLink'))->toBeNull();
})->with([[[]], [['completed' => 'all']]]);

test('each role gets exactly Task::visibleTo()\'s tasks on the project, within the window', function (string $who) {
    $hiddenDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Finance', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Other project', 'description' => 'd']);

    // Staff: department access, assignee-only, subtask-assignee-only.
    $granted = makeWindowTask($this, 'pending');
    $assigneeOnly = makeWindowTask($this, 'pending', null, ['department_id' => $hiddenDept->id, 'assignee_id' => $this->staff->id]);
    $subtaskOnly = makeWindowTask($this, 'pending', null, ['department_id' => $hiddenDept->id]);
    Subtask::create(['task_id' => $subtaskOnly->id, 'title' => 'Mine', 'assignee_id' => $this->staff->id]);
    $notVisibleToStaff = makeWindowTask($this, 'pending', null, ['department_id' => $hiddenDept->id]);
    makeWindowTask($this, 'completed', '2026-10-28 08:00:00');
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00');
    // Another project's tasks never appear.
    makeWindowTask($this, 'pending', null, ['project_id' => $otherProject->id]);

    $user = $this->{$who};
    $expected = drilldownExpectedIds($this, $user);
    expect(projectDrilldownIds($user, $this->project))->toBe($expected);

    if ($who === 'staff') {
        expect($expected)->toContain($granted->id, $assigneeOnly->id, $subtaskOnly->id)->not->toContain($notVisibleToStaff->id);
    } else {
        // Everything on the project except the old completed one.
        expect($expected)->toHaveCount(5);
    }
})->with(['management', 'staff', 'client', 'owner']);

test('a staff member with no department access sees only what they are assigned', function () {
    $loner = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $loner->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    makeWindowTask($this, 'pending');
    $theirs = makeWindowTask($this, 'pending', null, ['assignee_id' => $loner->id]);

    expect(projectDrilldownIds($loner, $this->project))->toBe([$theirs->id]);
});

test('a client not on the project gets 403', function () {
    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Not theirs', 'description' => 'd']);
    makeWindowTask($this, 'pending', null, ['project_id' => $otherProject->id]);

    $this->actingAs($this->client)->get(route('projects.tasks', $otherProject))->assertForbidden();
});

test('a project in a company the user is not in is 404', function () {
    $elsewhere = Organization::create(['name' => 'Elsewhere', 'slug' => 'elsewhere', 'accent_color' => '#000000']);
    $project = Project::create(['organization_id' => $elsewhere->id, 'name' => 'Theirs', 'description' => 'd']);

    foreach (['management', 'staff', 'client'] as $who) {
        $this->actingAs($this->{$who})->get(route('projects.tasks', $project))->assertNotFound();
    }
});

test('a project in an inactive company is 404, for the owner too', function () {
    makeWindowTask($this, 'pending');
    $this->org->update(['is_active' => false]);

    foreach (['owner', 'management', 'staff', 'client'] as $who) {
        $this->actingAs($this->{$who})->get(route('projects.tasks', $this->project))->assertNotFound();
    }
});

test('a guest is sent to the login page', function () {
    $this->get(route('projects.tasks', $this->project))->assertRedirect(route('login'));
});

test('task links carry the Projects page URL they were given, filters, sort and mode included', function () {
    $task = makeWindowTask($this, 'pending');
    $pageUrl = route('projects.index', [$this->org, 'q' => 'Proj', 'sort' => 'tasks', 'direction' => 'desc', 'completed' => 'all']);

    $response = projectDrilldown($this->management, $this->project, ['completed' => 'all', 'return_to' => $pageUrl, 'return_label' => 'Projects']);

    expect($response->getContent())->toContain('href="'.e(route('tasks.edit', ['task' => $task, 'return_to' => $pageUrl, 'return_label' => 'Projects'])).'"');

    // ...and "← Back" on the task page goes there.
    $this->actingAs($this->management)
        ->get(route('tasks.edit', ['task' => $task, 'return_to' => $pageUrl, 'return_label' => 'Projects']))
        ->assertOk()
        ->assertSee('href="'.e($pageUrl).'"', false);
});

test('an off-site or missing return URL falls back to the Projects page', function (array $query) {
    $task = makeWindowTask($this, 'pending');

    $content = projectDrilldown($this->management, $this->project, $query)->getContent();

    expect($content)->toContain('href="'.e(route('tasks.edit', ['task' => $task, 'return_to' => route('projects.index', $this->org), 'return_label' => 'Projects'])).'"')
        ->not->toContain('evil.example');
})->with([
    'off-site' => [['return_to' => 'https://evil.example/projects', 'return_label' => 'Projects']],
    'protocol-relative' => [['return_to' => '//evil.example/projects', 'return_label' => 'Projects']],
    'missing' => [[]],
]);

/**
 * The URL the page's script fetches for the first row: the row's own
 * data-tasks-url plus the table's shared data-tasks-query.
 */
function drilldownRequestUrl(string $html): string
{
    preg_match('/data-tasks-url="([^"]+)"/', $html, $url);
    preg_match('/data-tasks-query="([^"]*)"/', $html, $query);

    return html_entity_decode($url[1]).'?'.html_entity_decode($query[1]);
}

test('the Projects page hands each row its own URL and mode for the drilldown', function () {
    makeWindowTask($this, 'pending', null, ['title' => 'Not on the page']);
    $query = ['q' => 'Proj', 'sort' => 'tasks', 'direction' => 'desc', 'completed' => 'all'];

    $response = $this->actingAs($this->management)->get(route('projects.index', [$this->org] + $query))->assertOk();
    $html = $response->getContent();

    expect($html)->not->toContain('Not on the page')
        ->and($response->viewData('projects')->first()->relationLoaded('tasks'))->toBeFalse()
        // The shared query is rendered once, not repeated in every row.
        ->and($html)->not->toMatch('/data-tasks-url="[^"]*\?/')
        ->and(substr_count($html, 'data-tasks-query='))->toBe(1);

    $url = drilldownRequestUrl($html);
    parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

    expect(parse_url($url, PHP_URL_PATH))->toBe(parse_url(route('projects.tasks', $this->project), PHP_URL_PATH))
        ->and($params['completed'])->toBe('all')
        ->and($params['return_label'])->toBe('Projects');

    // url()->full(): the page's own URL, query in its own (sorted) order.
    expect(parse_url($params['return_to'], PHP_URL_PATH))->toBe(parse_url(route('projects.index', $this->org), PHP_URL_PATH));
    parse_str((string) parse_url($params['return_to'], PHP_URL_QUERY), $returnQuery);
    expect($returnQuery)->toEqualCanonicalizing($query);
});

test('in the default mode the page does not pass completed to the drilldown', function () {
    $html = $this->actingAs($this->management)->get(route('projects.index', $this->org))->assertOk()->getContent();

    parse_str((string) parse_url(drilldownRequestUrl($html), PHP_URL_QUERY), $params);

    expect($params)->not->toHaveKey('completed')
        ->and($params['return_to'])->toBe(route('projects.index', $this->org));
});
