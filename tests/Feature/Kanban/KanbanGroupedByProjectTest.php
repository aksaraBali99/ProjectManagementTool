<?php

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\KanbanController;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * SOL-0025 phase 1: the Kanban board is one collapsible lane per project,
 * sorted by name. The page renders the lanes' counts but no cards; a
 * lane's cards come from kanban.project when it is first opened, Load more
 * continues one project (?project=), and the poll returns the lane list
 * and each card's project.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));
    createOwner();

    $this->org = Organization::create(['name' => 'Lane Org', 'slug' => 'lane-org', 'code' => 'LAN', 'accent_color' => '#1D9E75']);
    $this->deptA = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptB = Department::create(['organization_id' => $this->org->id, 'name' => 'Operations', 'color' => '#000000']);

    // Created out of name order; "beta" is lower case, so a case-sensitive
    // sort would put it last.
    $this->gamma = Project::create(['organization_id' => $this->org->id, 'name' => 'Gamma', 'description' => 'd']);
    $this->beta = Project::create(['organization_id' => $this->org->id, 'name' => 'beta', 'description' => 'd']);
    $this->alpha = Project::create(['organization_id' => $this->org->id, 'name' => 'Alpha', 'description' => 'd']);

    $role = fn (string $slug) => Role::where('slug', $slug)->firstOrFail()->id;
    $member = function (string $slug, ?Organization $org = null) use ($role) {
        $user = User::factory()->create();
        OrgMember::create(['organization_id' => ($org ?? $this->org)->id, 'user_id' => $user->id, 'role_id' => $role($slug)]);

        return $user;
    };

    $this->management = $member('management');

    // Staff: Marketing only.
    $this->staff = $member('staff');
    AccessPermission::create(['user_id' => $this->staff->id, 'organization_id' => $this->org->id, 'department_id' => $this->deptA->id, 'allowed' => true]);

    // Client: on Beta only.
    $this->client = $member('client');
    $this->beta->clients()->attach($this->client->id);

    $this->otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org', 'code' => 'OTH', 'accent_color' => '#000000']);
    $otherDept = Department::create(['organization_id' => $this->otherOrg->id, 'name' => 'Sales', 'color' => '#000000']);
    $this->otherProject = Project::create(['organization_id' => $this->otherOrg->id, 'name' => 'Elsewhere', 'description' => 'd']);
    Task::create([
        'organization_id' => $this->otherOrg->id, 'project_id' => $this->otherProject->id, 'department_id' => $otherDept->id,
        'title' => 'Elsewhere task', 'priority' => 'high', 'status' => 'pending',
    ]);
});

function laneTask(object $context, Project $project, string $status, array $attributes = []): Task
{
    $task = Task::create($attributes + [
        'organization_id' => $context->org->id,
        'project_id' => $project->id,
        'department_id' => $context->deptA->id,
        'title' => 'Task in '.$project->name,
        'priority' => 'medium',
        'status' => $status,
    ]);

    return $task;
}

/** Completed at a given time (the observer stamps now() on save). */
function laneCompletedTask(object $context, Project $project, string $completedAt, array $attributes = []): Task
{
    $task = laneTask($context, $project, 'completed', $attributes);
    DB::table('tasks')->where('id', $task->id)->update(['completed_at' => $completedAt]);

    return $task->fresh();
}

function laneChanges(object $context, User $user, int $since, array $query = []): array
{
    return $context->actingAs($user)
        ->getJson(route('kanban.changes', [$context->org, 'since' => $since] + $query))
        ->assertOk()
        ->json();
}

/** The lane counts the board and the poll share, keyed by status. */
function laneTotals(int $pending, int $inProgress, int $inReview, int $completed): array
{
    return ['pending' => $pending, 'in_progress' => $inProgress, 'in_review' => $inReview, 'completed' => $completed];
}

// --- The board page ------------------------------------------------------

test('the board lists one lane per project with visible tasks, sorted by name, with its counts; the headers show the company totals; no cards', function () {
    laneTask($this, $this->gamma, 'pending');
    laneTask($this, $this->gamma, 'pending');
    laneTask($this, $this->gamma, 'in_review');
    laneTask($this, $this->beta, 'in_progress');
    laneTask($this, $this->alpha, 'completed');
    laneTask($this, $this->alpha, 'pending');
    // A project with no tasks gets no lane.
    Project::create(['organization_id' => $this->org->id, 'name' => 'Empty', 'description' => 'd']);

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    $lanes = $response->viewData('lanes');

    expect($lanes->pluck('name')->all())->toBe(['Alpha', 'beta', 'Gamma'])
        ->and($lanes->pluck('totals')->all())->toBe([
            laneTotals(1, 0, 0, 1),
            laneTotals(0, 1, 0, 0),
            laneTotals(2, 0, 1, 0),
        ])
        ->and($lanes->pluck('total')->all())->toBe([2, 1, 3])
        ->and($response->viewData('totals'))->toBe(laneTotals(3, 1, 1, 1));

    $html = withoutScripts($response->getContent());

    // Lanes in that order, each with its "N tasks" and count per status.
    $positions = array_map(fn (Project $project) => strpos($html, 'data-project-id="'.$project->id.'"'), [$this->alpha, $this->beta, $this->gamma]);
    expect($positions)->not->toContain(false);
    $sorted = $positions;
    sort($sorted);
    expect($positions)->toBe($sorted);
    expect($html)->toContain('>3 tasks</p>')->toContain('>1 task</p>')
        ->toMatch('/data-project-id="'.$this->gamma->id.'".*?data-status="pending">2<\/span>/s');

    // Headers: the company total per status, and the number of projects.
    expect($html)->toMatch('/'.preg_quote(TaskStatus::Pending->label(), '/').'<\/span>\s*<span[^>]*>3<\/span>/')
        ->toMatch('/<span id="kanban-project-count"[^>]*>3<\/span>/');

    // No cards: every lane starts closed and loads its cards when opened.
    expect(kanbanCardIds($response->getContent()))->toBe([])
        ->and($html)->not->toContain('Task in Gamma')
        ->and(substr_count($html, 'aria-expanded="false"'))->toBe(3 + 1) // + the lane template
        ->and($html)->toContain('aria-label="Open Gamma"');
});

test('lanes sort by name without case, then id', function () {
    $twin = Project::create(['organization_id' => $this->org->id, 'name' => 'ALPHA', 'description' => 'd']);
    laneTask($this, $twin, 'pending');
    laneTask($this, $this->alpha, 'pending');
    laneTask($this, $this->beta, 'pending');

    $ids = kanbanLanes($this->management, $this->org->id)->pluck('id')->all();

    expect($ids)->toBe([$this->alpha->id, $twin->id, $this->beta->id]);
});

test('staff and clients get lanes only for projects they can see tasks in, counting only those tasks', function () {
    laneTask($this, $this->alpha, 'pending');                                          // staff: Marketing
    laneTask($this, $this->alpha, 'pending', ['department_id' => $this->deptB->id]);  // hidden from staff
    laneTask($this, $this->beta, 'in_review', ['department_id' => $this->deptB->id]); // hidden from staff
    laneTask($this, $this->beta, 'pending');                                           // staff: Marketing
    laneTask($this, $this->gamma, 'pending', ['department_id' => $this->deptB->id]);  // hidden from staff
    // Staff also see a task they are assigned, outside their departments.
    laneTask($this, $this->gamma, 'in_progress', ['department_id' => $this->deptB->id, 'assignee_id' => $this->staff->id]);

    $staffLanes = kanbanLanes($this->staff, $this->org->id)->keyBy('name');
    expect($staffLanes->keys()->all())->toBe(['Alpha', 'beta', 'Gamma'])
        ->and($staffLanes['Alpha']['totals'])->toBe(laneTotals(1, 0, 0, 0))
        ->and($staffLanes['beta']['totals'])->toBe(laneTotals(1, 0, 0, 0))
        ->and($staffLanes['Gamma']['totals'])->toBe(laneTotals(0, 1, 0, 0));

    // A staff member who sees nothing of a project gets no lane for it.
    $this->staff->accessPermissions()->delete();
    expect(kanbanLanes($this->staff, $this->org->id)->pluck('name')->all())->toBe(['Gamma']);

    // The client: their project only, every task of it.
    $clientLanes = kanbanLanes($this->client, $this->org->id);
    expect($clientLanes->pluck('name')->all())->toBe(['beta'])
        ->and($clientLanes->first()['totals'])->toBe(laneTotals(1, 0, 1, 0));
    $this->actingAs($this->client)->get(route('kanban', $this->org))->assertOk()
        ->assertDontSee('Alpha')->assertDontSee('Gamma');
});

test('a closed project\'s lane shows the "Closed" tag; an open one hides it', function () {
    $this->beta->update(['status' => ProjectStatus::Closed]);
    laneTask($this, $this->alpha, 'pending');
    laneTask($this, $this->beta, 'pending');

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    expect($response->viewData('lanes')->pluck('closed', 'name')->all())->toBe(['Alpha' => false, 'beta' => true]);

    $html = withoutScripts($response->getContent());
    preg_match_all('/<span class="kanban-lane-closed[^"]*"([^>]*)>Closed<\/span>/', $html, $tags);
    // Alpha, beta, then the lane template.
    expect($tags[1])->toHaveCount(3)
        ->and($tags[1][0])->toContain('hidden')
        ->and($tags[1][1])->not->toContain('hidden');
});

test('a project with only old completed tasks has no lane, but one under ?completed=all; a lane counts its window and the rest as older', function () {
    // Window: 14 days, so before Nov 6.
    laneCompletedTask($this, $this->alpha, '2026-09-01 09:00:00');
    laneCompletedTask($this, $this->alpha, '2026-09-02 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-11-18 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-10-01 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-10-02 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-10-03 09:00:00');

    $lanes = kanbanLanes($this->management, $this->org->id);
    expect($lanes->pluck('name')->all())->toBe(['beta'])
        ->and($lanes->first()['totals']['completed'])->toBe(1)
        ->and($lanes->first()['older'])->toBe(3);

    $html = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->getContent());
    expect($html)->toMatch('/3 older completed ·\s*<a href="'.preg_quote(e(route('kanban', [$this->org, 'completed' => 'all'])), '/').'"[^>]*>Show all<\/a>/')
        ->not->toContain('Showing all completed tasks');

    $all = kanbanLanes($this->management, $this->org->id, ['completed' => 'all']);
    expect($all->pluck('name')->all())->toBe(['Alpha', 'beta'])
        ->and($all->pluck('totals')->pluck('completed')->all())->toBe([2, 4])
        ->and($all->pluck('older')->all())->toBe([0, 0]);

    $allHtml = withoutScripts($this->actingAs($this->management)->get(route('kanban', [$this->org, 'completed' => 'all']))->getContent());
    expect($allHtml)->toContain('Showing all completed tasks')
        ->toMatch('/Show recent only/')
        ->not->toContain('older completed');
});

test('the poll\'s company older also counts projects that have no lane', function () {
    laneCompletedTask($this, $this->alpha, '2026-09-01 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-11-18 09:00:00');
    laneCompletedTask($this, $this->beta, '2026-10-01 09:00:00');

    $json = laneChanges($this, $this->management, now()->getTimestamp());

    expect(collect($json['lanes'])->pluck('id')->all())->toBe([$this->beta->id])
        ->and($json['lanes'][0]['older'])->toBe(1)
        ->and($json['older'])->toBe(2)
        ->and($json['totals']['completed'])->toBe(1);
});

// --- kanban.project: one lane's cards --------------------------------------

test('kanban.project returns the project\'s first 30 cards per status in the column order, with total, remaining and older', function () {
    // Pending: 35, in an order unrelated to creation.
    $priorities = ['low', 'high', 'medium'];
    $dueDates = ['2026-12-01', null, '2026-11-25', '2026-11-25'];
    for ($i = 0; $i < 35; $i++) {
        laneTask($this, $this->alpha, 'pending', ['priority' => $priorities[$i % 3], 'due_date' => $dueDates[$i % 4]]);
    }
    // Completed: newest first, one outside the window.
    $first = laneCompletedTask($this, $this->alpha, '2026-11-10 09:00:00');
    $second = laneCompletedTask($this, $this->alpha, '2026-11-19 09:00:00');
    laneCompletedTask($this, $this->alpha, '2026-09-01 09:00:00');
    // Another project's cards never appear.
    $elsewhere = laneTask($this, $this->beta, 'pending', ['priority' => 'high']);

    $lane = kanbanLane($this->management, $this->org->id, $this->alpha->id);

    $priorityRank = ['high' => 0, 'medium' => 1, 'low' => 2];
    $expectedPending = Task::where('project_id', $this->alpha->id)->where('status', 'pending')->get()
        ->sort(fn (Task $a, Task $b) => [$priorityRank[$a->priority->value], $a->due_date === null, $a->due_date?->timestamp ?? 0, $a->id]
            <=> [$priorityRank[$b->priority->value], $b->due_date === null, $b->due_date?->timestamp ?? 0, $b->id])
        ->pluck('id')->all();

    expect(kanbanCardIds($lane['columns']['pending']['html']))->toBe(array_slice($expectedPending, 0, KanbanController::COLUMN_BATCH))
        ->and($lane['columns']['pending']['total'])->toBe(35)
        ->and($lane['columns']['pending']['remaining'])->toBe(5)
        ->and(kanbanCardIds($lane['columns']['completed']['html']))->toBe([$second->id, $first->id])
        ->and($lane['columns']['completed']['total'])->toBe(2)
        ->and($lane['columns']['completed']['remaining'])->toBe(0)
        ->and($lane['columns']['in_progress'])->toBe(['html' => '', 'total' => 0, 'remaining' => 0])
        ->and($lane['older'])->toBe(1)
        ->and(array_keys($lane['columns']))->toBe(array_map(fn (TaskStatus $status) => $status->value, TaskStatus::cases()));

    $allCards = collect($lane['columns'])->pluck('html')->implode('');
    expect(kanbanCardIds($allCards))->not->toContain($elsewhere->id);

    // ?completed=all: every completed task, nothing older.
    $all = kanbanLane($this->management, $this->org->id, $this->alpha->id, ['completed' => 'all']);
    expect($all['columns']['completed']['total'])->toBe(3)->and($all['older'])->toBe(0);
});

test('kanban.project shows only the cards the viewer may see, with their own permissions', function () {
    $mine = laneTask($this, $this->alpha, 'pending', ['assignee_id' => $this->staff->id]);
    $theirs = laneTask($this, $this->alpha, 'pending');
    $hidden = laneTask($this, $this->alpha, 'pending', ['department_id' => $this->deptB->id]);

    $html = kanbanLane($this->staff, $this->org->id, $this->alpha->id)['columns']['pending']['html'];

    expect(kanbanCardIds($html))->toEqualCanonicalizing([$mine->id, $theirs->id])
        ->and(kanbanCardMarkup($html, $mine->id))->toContain('draggable="true"')
        ->and(kanbanCardMarkup($html, $theirs->id))->toContain('draggable="false"')
        ->and(kanbanCardIds($html))->not->toContain($hidden->id);
});

test('kanban.project refuses who the board refuses, 404s another company\'s project, and gives an empty lane rather than an error', function () {
    laneTask($this, $this->alpha, 'pending');
    $url = route('kanban.project', [$this->org, $this->alpha]);

    // Another company's project, or no such project.
    $this->actingAs($this->management)->getJson(route('kanban.project', [$this->org, $this->otherProject]))->assertNotFound();
    $this->actingAs($this->management)->getJson(route('kanban.project', [$this->org, 999999]))->assertNotFound();
    $this->actingAs($this->management)->getJson(route('kanban.project', [$this->org, 'abc']))->assertNotFound();

    // A project with no visible tasks: empty columns, 200 (a lane can
    // empty out between page load and open).
    $empty = kanbanLane($this->client, $this->org->id, $this->alpha->id);
    expect($empty['columns'])->each->toBe(['html' => '', 'total' => 0, 'remaining' => 0])
        ->and($empty['older'])->toBe(0);
    $this->gamma->tasks()->delete();
    expect(kanbanLane($this->management, $this->org->id, $this->gamma->id)['columns']['pending']['total'])->toBe(0);

    // Not a board company of the caller's.
    $outsider = User::factory()->create();
    OrgMember::create(['organization_id' => $this->otherOrg->id, 'user_id' => $outsider->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    $this->actingAs($outsider)->getJson($url)->assertForbidden();

    // view_kanban revoked.
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $staffRole->permissions()->sync($staffRole->permissions()->where('slug', '!=', 'view_kanban')->pluck('permissions.id')->all());
    $this->actingAs($this->staff)->getJson($url)->assertForbidden();

    // An inactive company: 403 before any 404.
    $this->org->update(['is_active' => false]);
    $this->actingAs($this->management)->getJson($url)->assertForbidden();
    $this->actingAs($this->management)->getJson(route('kanban.project', [$this->org, $this->otherProject]))->assertForbidden();
    $this->org->update(['is_active' => true]);

    // Guests.
    auth()->logout();
    $this->getJson($url)->assertUnauthorized();
});

// --- Load more inside a lane --------------------------------------------------

test('kanban.column with ?project= continues only that project', function () {
    for ($i = 0; $i < 40; $i++) {
        laneTask($this, $this->alpha, 'pending');
    }
    for ($i = 0; $i < 10; $i++) {
        laneTask($this, $this->beta, 'pending');
    }

    $first = kanbanLaneColumn($this->management, $this->org->id, $this->alpha->id, TaskStatus::Pending);
    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending', 'project' => $this->alpha->id, 'offset' => 30]))
        ->assertOk()
        ->json();

    $alphaIds = Task::where('project_id', $this->alpha->id)->orderBy('id')->pluck('id')->all();
    $loaded = kanbanCardIds($json['html']);
    expect($loaded)->toHaveCount(10)
        ->and($json['remaining'])->toBe(0)
        ->and(array_merge($first['ids'], $loaded))->toEqualCanonicalizing($alphaIds);

    // Without ?project=, the whole company's column, as before.
    $company = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending', 'offset' => 30]))
        ->assertOk()
        ->json();
    expect(kanbanCardIds($company['html']))->toHaveCount(20)->and($company['remaining'])->toBe(0);

    // Another company's project: 404. One of this company's the viewer
    // sees nothing of: empty, 200.
    $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending', 'project' => $this->otherProject->id]))
        ->assertNotFound();
    $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending', 'project' => 'abc']))
        ->assertNotFound();
    $none = $this->actingAs($this->client)
        ->getJson(route('kanban.column', [$this->org, 'pending', 'project' => $this->alpha->id]))
        ->assertOk()
        ->json();
    expect($none)->toBe(['html' => '', 'remaining' => 0]);
});

test('each lane\'s Load more and lane URLs name its project, and carry completed=all in that mode', function () {
    laneTask($this, $this->alpha, 'pending');

    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->getContent();
    expect($html)->toContain('data-url="'.e(route('kanban.project', [$this->org, $this->alpha])).'"')
        ->toContain('data-url="'.e(route('kanban.column', [$this->org, 'in_review', 'project' => $this->alpha->id])).'"');

    $all = $this->actingAs($this->management)->get(route('kanban', [$this->org, 'completed' => 'all']))->getContent();
    expect($all)->toContain('data-url="'.e(route('kanban.project', [$this->org, $this->alpha, 'completed' => 'all'])).'"')
        ->toContain('data-url="'.e(route('kanban.column', [$this->org, 'in_review', 'project' => $this->alpha->id, 'completed' => 'all'])).'"');
});

// --- Live updates --------------------------------------------------------------

test('the poll gives each card its project and lists the lanes in name order with totals and older', function () {
    laneTask($this, $this->gamma, 'pending');
    laneCompletedTask($this, $this->beta, '2026-09-01 09:00:00');
    laneTask($this, $this->beta, 'in_review');
    // Past the poll's 10-second overlap, so only the next task is "changed".
    $this->travel(20)->seconds();
    $since = now()->getTimestamp();
    $this->travel(5)->seconds();
    $changed = laneTask($this, $this->alpha, 'in_progress');
    $this->travel(5)->seconds();

    $json = laneChanges($this, $this->management, $since);

    expect($json['cards'])->toHaveCount(1)
        ->and($json['cards'][0]['id'])->toBe($changed->id)
        ->and($json['cards'][0]['project_id'])->toBe($this->alpha->id)
        ->and($json['lanes'])->toBe([
            ['id' => $this->alpha->id, 'name' => 'Alpha', 'closed' => false, 'totals' => laneTotals(0, 1, 0, 0), 'older' => 0],
            ['id' => $this->beta->id, 'name' => 'beta', 'closed' => false, 'totals' => laneTotals(0, 0, 1, 0), 'older' => 1],
            ['id' => $this->gamma->id, 'name' => 'Gamma', 'closed' => false, 'totals' => laneTotals(1, 0, 0, 0), 'older' => 0],
        ])
        ->and($json['totals'])->toBe(laneTotals(1, 1, 1, 0));
});

test('a task moved to another project comes back with its new project, and both lanes\' counts change', function () {
    $task = laneTask($this, $this->alpha, 'pending');
    laneTask($this, $this->alpha, 'pending');
    laneTask($this, $this->beta, 'pending');
    $since = now()->getTimestamp();
    $this->travel(5)->seconds();

    $task->update(['project_id' => $this->beta->id]);
    $this->travel(5)->seconds();

    $json = laneChanges($this, $this->management, $since);
    $lanes = collect($json['lanes'])->keyBy('name');

    expect(collect($json['cards'])->firstWhere('id', $task->id)['project_id'])->toBe($this->beta->id)
        ->and($lanes['Alpha']['totals']['pending'])->toBe(1)
        ->and($lanes['beta']['totals']['pending'])->toBe(2)
        ->and($json['removed'])->toBe([]);
});

test('a task in a project with no lane yet adds that lane; deactivating a lane\'s last task removes it', function () {
    laneTask($this, $this->alpha, 'pending');
    // Past the poll's 10-second overlap, so only the next task is "changed".
    $this->travel(20)->seconds();
    $since = now()->getTimestamp();
    $this->travel(5)->seconds();

    $first = laneTask($this, $this->beta, 'pending');
    $this->travel(5)->seconds();

    $json = laneChanges($this, $this->management, $since);
    expect(collect($json['lanes'])->pluck('id')->all())->toBe([$this->alpha->id, $this->beta->id])
        ->and($json['cards'][0]['project_id'])->toBe($this->beta->id);

    $since = $json['cursor'];
    $this->travel(20)->seconds();
    $first->delete();
    $this->travel(5)->seconds();

    $json = laneChanges($this, $this->management, $since);
    expect(collect($json['lanes'])->pluck('id')->all())->toBe([$this->alpha->id])
        ->and($json['removed'])->toBe([$first->id])
        ->and($json['cards'])->toBe([]);
});

test('the poll lists only the viewer\'s own lanes and counts', function () {
    laneTask($this, $this->alpha, 'pending', ['department_id' => $this->deptB->id]);
    laneTask($this, $this->beta, 'pending');
    laneTask($this, $this->beta, 'pending', ['department_id' => $this->deptB->id]);

    $staff = laneChanges($this, $this->staff, now()->getTimestamp());
    expect($staff['lanes'])->toBe([
        ['id' => $this->beta->id, 'name' => 'beta', 'closed' => false, 'totals' => laneTotals(1, 0, 0, 0), 'older' => 0],
    ]);

    $client = laneChanges($this, $this->client, now()->getTimestamp());
    expect(collect($client['lanes'])->pluck('totals')->all())->toBe([laneTotals(2, 0, 0, 0)]);
});

// --- The card and the lane template -------------------------------------------

test('the card no longer shows the project name; it still shows the code, title and due date', function () {
    $task = laneTask($this, $this->alpha, 'pending', ['title' => 'Write the brief', 'due_date' => '2026-12-05']);
    $undated = laneTask($this, $this->alpha, 'pending', ['title' => 'No date']);

    $html = kanbanLane($this->management, $this->org->id, $this->alpha->id)['columns']['pending']['html'];
    $card = kanbanCardMarkup($html, $task->id);

    expect($card)->toContain($task->fresh()->code.' </span>Write the brief')
        ->toContain('Due Dec 5')
        ->not->toContain('Alpha');
    expect(kanbanCardMarkup($html, $undated->id))->not->toContain('Due ')->not->toContain('Alpha');
});

test('project names are escaped in the lanes, and the lane template is in the page with placeholders and no name', function () {
    $tricky = Project::create(['organization_id' => $this->org->id, 'name' => '<b>Bold</b> & "Co"', 'description' => 'd']);
    laneTask($this, $tricky, 'pending');

    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();

    expect($html)->not->toContain('<b>Bold</b>')
        ->toContain('title="&lt;b&gt;Bold&lt;/b&gt; &amp; &quot;Co&quot;">&lt;b&gt;Bold&lt;/b&gt; &amp; &quot;Co&quot;</span>')
        ->toContain('aria-label="Open &lt;b&gt;Bold&lt;/b&gt; &amp; &quot;Co&quot;"');

    preg_match('/<template id="kanban-lane-template">(.*?)<\/template>/s', $html, $template);
    expect($template)->not->toBeEmpty();
    expect($template[1])->toContain('data-project-id="__PROJECT__"')
        ->toContain('data-name=""')
        ->toContain(e(route('kanban.project', [$this->org, '__PROJECT__'])))
        ->toContain(e(route('kanban.column', [$this->org, 'pending', 'project' => '__PROJECT__'])))
        ->toContain('aria-controls="kanban-lane-body-__PROJECT__"')
        ->not->toContain('Bold');

    // The script sets a new lane's name as text, never as HTML.
    expect($html)->toContain('name.textContent = info.name;')
        ->not->toContain('innerHTML = info.name');
});

test('with no visible tasks the board shows its "No tasks" message and no lanes', function () {
    $html = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent());

    expect($html)->toMatch('/<p id="kanban-no-lanes"[^>]*>No tasks<\/p>/')
        ->not->toMatch('/<p id="kanban-no-lanes"[^>]*hidden/')
        ->and(substr_count($html, '<section class="kanban-lane '))->toBe(1); // the template only

    laneTask($this, $this->alpha, 'pending');
    $html = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->getContent());
    expect($html)->toMatch('/<p id="kanban-no-lanes"[^>]*hidden/');
});
