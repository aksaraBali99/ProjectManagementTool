<?php

use App\Http\Controllers\KanbanController;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;

/**
 * SOL-0025 phase 2: the Kanban lanes a user left open are saved per user
 * and company (users.kanban_open_lanes), through PUT kanban.open-lanes,
 * and the board opens them again on the next visit.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-24 09:00:00'));
    createOwner();

    $role = fn (string $slug) => Role::where('slug', $slug)->firstOrFail()->id;

    $this->org = Organization::create(['name' => 'Lane Org', 'slug' => 'lane-org', 'code' => 'LAN', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->gamma = Project::create(['organization_id' => $this->org->id, 'name' => 'Gamma', 'description' => 'd']);
    $this->alpha = Project::create(['organization_id' => $this->org->id, 'name' => 'Alpha', 'description' => 'd']);
    $this->beta = Project::create(['organization_id' => $this->org->id, 'name' => 'Beta', 'description' => 'd']);

    $this->otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org', 'code' => 'OTH', 'accent_color' => '#000000']);
    $this->otherDept = Department::create(['organization_id' => $this->otherOrg->id, 'name' => 'Sales', 'color' => '#000000']);
    $this->otherProject = Project::create(['organization_id' => $this->otherOrg->id, 'name' => 'Elsewhere', 'description' => 'd']);

    $this->management = User::factory()->create();
    foreach ([$this->org, $this->otherOrg] as $org) {
        OrgMember::create(['organization_id' => $org->id, 'user_id' => $this->management->id, 'role_id' => $role('management')]);
    }

    foreach ([$this->alpha, $this->beta, $this->gamma] as $project) {
        openLaneTask($this->org, $this->dept, $project);
    }
    openLaneTask($this->otherOrg, $this->otherDept, $this->otherProject);
});

function openLaneTask(Organization $org, Department $dept, Project $project, string $status = 'pending'): Task
{
    return Task::create([
        'organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id,
        'title' => 'Task in '.$project->name, 'priority' => 'medium', 'status' => $status,
    ]);
}

function saveOpenLanes(User $user, Organization $org, mixed $projectIds): TestResponse
{
    return test()->actingAs($user)->putJson(route('kanban.open-lanes', $org), ['project_ids' => $projectIds]);
}

function storedOpenLanes(User $user): ?array
{
    return $user->fresh()->kanban_open_lanes;
}

test('saving stores this company\'s open lanes; the board then opens them, in lane order, and still renders no cards', function () {
    saveOpenLanes($this->management, $this->org, [$this->gamma->id, $this->alpha->id])->assertNoContent();

    expect(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->gamma->id, $this->alpha->id]]);

    foreach ([[], ['completed' => 'all']] as $query) {
        $response = $this->actingAs($this->management)->get(route('kanban', [$this->org] + $query))->assertOk();

        // Lane order is by name: Alpha before Gamma.
        expect($response->viewData('openLaneIds'))->toBe([$this->alpha->id, $this->gamma->id]);
        $html = $response->getContent();
        expect($html)->toContain('data-open-lanes="'.e(json_encode([$this->alpha->id, $this->gamma->id])).'"')
            ->toContain('data-open-lanes-url="'.e(route('kanban.open-lanes', $this->org)).'"')
            ->and(kanbanCardIds($html))->toBe([]);
    }
});

test('a first visit, or a company never saved, opens nothing', function () {
    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();

    expect($response->viewData('openLaneIds'))->toBe([])
        ->and($response->viewData('savedLaneIds'))->toBe([])
        ->and($response->getContent())->toContain('data-open-lanes="[]"')
        ->toMatch('/<button type="button" id="kanban-collapse-all"[^>]*\sdisabled\s*>/')
        ->not->toMatch('/<button type="button" id="kanban-expand-all"[^>]*\sdisabled\s*>/');
});

test('each company keeps its own list', function () {
    saveOpenLanes($this->management, $this->org, [$this->alpha->id])->assertNoContent();
    saveOpenLanes($this->management, $this->otherOrg, [$this->otherProject->id])->assertNoContent();

    expect(storedOpenLanes($this->management))->toBe([
        (string) $this->org->id => [$this->alpha->id],
        (string) $this->otherOrg->id => [$this->otherProject->id],
    ]);
    expect($this->actingAs($this->management)->get(route('kanban', $this->org))->viewData('openLaneIds'))->toBe([$this->alpha->id])
        ->and($this->actingAs($this->management)->get(route('kanban', $this->otherOrg))->viewData('openLaneIds'))->toBe([$this->otherProject->id]);

    // Changing B leaves A as it was.
    saveOpenLanes($this->management, $this->otherOrg, [])->assertNoContent();
    expect(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->alpha->id]]);
});

test('an empty list removes the company\'s key, and removing the last key leaves null', function () {
    saveOpenLanes($this->management, $this->org, [$this->alpha->id])->assertNoContent();
    saveOpenLanes($this->management, $this->otherOrg, [$this->otherProject->id])->assertNoContent();

    saveOpenLanes($this->management, $this->org, [])->assertNoContent();
    expect(storedOpenLanes($this->management))->toBe([(string) $this->otherOrg->id => [$this->otherProject->id]]);

    saveOpenLanes($this->management, $this->otherOrg, [])->assertNoContent();
    expect(storedOpenLanes($this->management))->toBeNull();
    expect(DB::table('users')->where('id', $this->management->id)->value('kanban_open_lanes'))->toBeNull();
});

test('odd ids are dropped, not refused: other companies\' projects, non-integers, negatives, duplicates', function () {
    saveOpenLanes($this->management, $this->org, [
        $this->beta->id, 'a', -1, 2.5, null, true, [1], 0, $this->otherProject->id, $this->beta->id, (string) $this->alpha->id, 999999,
    ])->assertNoContent();

    expect(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->beta->id, $this->alpha->id]]);
});

test('more than 200 ids keeps the first 200', function () {
    $ids = [];
    foreach (range(1, KanbanController::OPEN_LANES_LIMIT + 10) as $i) {
        $ids[] = Project::create(['organization_id' => $this->org->id, 'name' => 'Extra '.$i, 'description' => 'd'])->id;
    }

    saveOpenLanes($this->management, $this->org, $ids)->assertNoContent();

    expect(storedOpenLanes($this->management)[(string) $this->org->id])->toBe(array_slice($ids, 0, KanbanController::OPEN_LANES_LIMIT));
});

test('project_ids missing or not an array is a 422, and nothing is saved', function (mixed $body) {
    $this->actingAs($this->management)->putJson(route('kanban.open-lanes', $this->org), $body)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('project_ids');

    expect(storedOpenLanes($this->management))->toBeNull();
})->with([
    'missing' => [[]],
    'a string' => [['project_ids' => '1,2']],
    'a number' => [['project_ids' => 5]],
    'null' => [['project_ids' => null]],
]);

test('the board\'s access rule applies: 403 without view_kanban or for an inactive company; guests are sent to log in', function () {
    $outsider = User::factory()->create();
    OrgMember::create(['organization_id' => $this->otherOrg->id, 'user_id' => $outsider->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    saveOpenLanes($outsider, $this->org, [$this->alpha->id])->assertForbidden();

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $staffRole->permissions()->sync($staffRole->permissions()->where('slug', '!=', 'view_kanban')->pluck('permissions.id')->all());
    saveOpenLanes($staff, $this->org, [$this->alpha->id])->assertForbidden();

    $this->org->update(['is_active' => false]);
    saveOpenLanes($this->management, $this->org, [$this->alpha->id])->assertForbidden();
    $this->org->update(['is_active' => true]);

    expect(storedOpenLanes($outsider))->toBeNull()
        ->and(storedOpenLanes($staff))->toBeNull()
        ->and(storedOpenLanes($this->management))->toBeNull();

    auth()->logout();
    $this->putJson(route('kanban.open-lanes', $this->org), ['project_ids' => [$this->alpha->id]])->assertUnauthorized();
    $this->put(route('kanban.open-lanes', $this->org), ['project_ids' => [$this->alpha->id]])->assertRedirect(route('login'));
});

test('a saved lane with no visible tasks now isn\'t opened, but stays saved (and reopens when it returns)', function () {
    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id])->assertNoContent();
    $this->beta->tasks()->get()->each->delete();

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    expect($response->viewData('openLaneIds'))->toBe([$this->alpha->id])
        ->and($response->viewData('savedLaneIds'))->toBe([$this->alpha->id, $this->beta->id])
        ->and($response->getContent())->toContain('data-saved-lanes="'.e(json_encode([$this->alpha->id, $this->beta->id])).'"');
    expect(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->alpha->id, $this->beta->id]]);

    // The script reopens a lane the poll adds back when its id is saved.
    expect($response->getContent())->toContain('if (savedOpen.has(String(info.id))) openLane(info.id);');

    openLaneTask($this->org, $this->dept, $this->beta);
    expect($this->actingAs($this->management)->get(route('kanban', $this->org))->viewData('openLaneIds'))->toBe([$this->alpha->id, $this->beta->id]);
});

test('a project moved to another company is dropped on the next save', function () {
    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id])->assertNoContent();
    $this->beta->update(['organization_id' => $this->otherOrg->id]);

    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id, $this->gamma->id])->assertNoContent();

    expect(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->alpha->id, $this->gamma->id]]);
});

test('saving the same list again (in any order) writes nothing', function () {
    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id])->assertNoContent();
    $updatedAt = DB::table('users')->where('id', $this->management->id)->value('updated_at');

    $this->travel(5)->minutes();
    saveOpenLanes($this->management, $this->org, [$this->beta->id, $this->alpha->id, $this->beta->id])->assertNoContent();

    expect(DB::table('users')->where('id', $this->management->id)->value('updated_at'))->toBe($updatedAt);

    // A real change does write.
    saveOpenLanes($this->management, $this->org, [$this->beta->id])->assertNoContent();
    expect(DB::table('users')->where('id', $this->management->id)->value('updated_at'))->not->toBe($updatedAt);
});

test('a user\'s save never changes another user\'s saved lanes', function () {
    $colleague = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $colleague->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    saveOpenLanes($colleague, $this->org, [$this->gamma->id])->assertNoContent();

    // No user id in the request: there is none to send.
    $this->actingAs($this->management)
        ->putJson(route('kanban.open-lanes', $this->org), ['project_ids' => [$this->alpha->id], 'user_id' => $colleague->id])
        ->assertNoContent();

    expect(storedOpenLanes($colleague))->toBe([(string) $this->org->id => [$this->gamma->id]])
        ->and(storedOpenLanes($this->management))->toBe([(string) $this->org->id => [$this->alpha->id]])
        ->and($this->actingAs($colleague)->get(route('kanban', $this->org))->viewData('openLaneIds'))->toBe([$this->gamma->id]);
});

test('the save action\'s own queries, after the board access check: the project check and the update, or only the check when nothing changed', function () {
    // Counted: queries on projects (the id check) and updates of users
    // (the save). The board access check and the session/auth middleware
    // are left out.
    $queries = 0;
    DB::listen(function ($query) use (&$queries) {
        if (preg_match('/from ["`]?projects["`]?|^update ["`]?users["`]?/i', $query->sql)) {
            $queries++;
        }
    });

    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id])->assertNoContent();
    expect($queries)->toBe(2);

    $queries = 0;
    saveOpenLanes($this->management, $this->org, [$this->beta->id, $this->alpha->id])->assertNoContent();
    expect($queries)->toBe(1);
});

test('the board shows Expand all / Collapse all, hidden with no lanes, each disabled when it would change nothing', function () {
    $html = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->getContent());
    expect($html)->toMatch('/<div id="kanban-lane-actions" class="ml-auto"\s*>/')
        ->toContain('>Expand all</button>')
        ->toContain('>Collapse all</button>');

    saveOpenLanes($this->management, $this->org, [$this->alpha->id, $this->beta->id, $this->gamma->id])->assertNoContent();
    $all = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->getContent());
    expect($all)->toMatch('/<button type="button" id="kanban-expand-all"[^>]*\sdisabled\s*>/')
        ->not->toMatch('/<button type="button" id="kanban-collapse-all"[^>]*\sdisabled\s*>/');

    Task::query()->get()->each->delete();
    $none = withoutScripts($this->actingAs($this->management)->get(route('kanban', $this->org))->getContent());
    expect($none)->toMatch('/<div id="kanban-lane-actions" class="ml-auto"\s+hidden\s*>/');
});

test('the migration adds a nullable kanban_open_lanes column to users', function () {
    expect(Schema::hasColumn('users', 'kanban_open_lanes'))->toBeTrue();

    $column = collect(Schema::getColumns('users'))->firstWhere('name', 'kanban_open_lanes');
    expect($column['nullable'])->toBeTrue()
        ->and(User::factory()->create()->fresh()->kanban_open_lanes)->toBeNull();
});
