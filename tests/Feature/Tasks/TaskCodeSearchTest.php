<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Support\TaskCodeQuery;
use Illuminate\Support\Facades\DB;

/**
 * task #118 phase 4: the Tasks page search box finds a task by its code
 * (BGS-0012, bgs-12, bgs12, 0012, 12, #12), within the same visibility as
 * the list, alongside the title search.
 */
beforeEach(function () {
    createOwner();
    $this->bgs = Organization::create(['name' => 'Bali Goods Sourcing', 'slug' => 'bgs', 'code' => 'BGS', 'accent_color' => '#1D9E75']);
    $this->slv = Organization::create(['name' => 'Solava', 'slug' => 'slv', 'code' => 'SLV', 'accent_color' => '#534AB7']);

    foreach (['bgs', 'slv'] as $key) {
        $this->{$key.'Dept'} = Department::create(['organization_id' => $this->{$key}->id, 'name' => 'Ops', 'color' => '#000000']);
        $this->{$key.'Project'} = Project::create(['organization_id' => $this->{$key}->id, 'name' => 'Project '.$key, 'description' => 'd']);
    }

    $this->management = User::factory()->create();
    foreach ([$this->bgs, $this->slv] as $organization) {
        OrgMember::create(['organization_id' => $organization->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    }

    // BGS-0012, BGS-0112, and "Q12 report" (BGS-0113).
    $this->task12 = searchTask($this, 'bgs', 12, 'Fix invoice PDF layout');
    $this->task112 = searchTask($this, 'bgs', 112, 'Unrelated work');
    $this->q12 = searchTask($this, 'bgs', null, 'Q12 report');
});

/** A task in $company, numbered $number when given. */
function searchTask(object $context, string $company, ?int $number, string $title, array $attributes = []): Task
{
    if ($number !== null) {
        DB::table('organizations')->where('id', $context->{$company}->id)->update(['next_task_number' => $number]);
    }

    return Task::create(array_merge([
        'organization_id' => $context->{$company}->id,
        'project_id' => $context->{$company.'Project'}->id,
        'department_id' => $context->{$company.'Dept'}->id,
        'title' => $title, 'priority' => 'medium', 'status' => 'pending',
    ], $attributes));
}

/** The ids the Tasks list shows for this search. */
function searchIds(object $context, User $user, Organization $organization, array $query): array
{
    $response = $context->actingAs($user)->get(route('tasks.index', [$organization] + $query))->assertOk();

    return collect($response->viewData('tasks')->items())->pluck('id')->sort()->values()->all();
}

// --- The parser --------------------------------------------------------

test('TaskCodeQuery::parse() reads every accepted form of a code', function (string $typed, ?string $code, int $number) {
    expect(TaskCodeQuery::parse($typed))->toBe(['code' => $code, 'number' => $number]);
})->with([
    ['BGS-0012', 'BGS', 12],
    ['BGS-12', 'BGS', 12],
    ['BGS12', 'BGS', 12],
    ['BGS 12', 'BGS', 12],
    ['bgs-0012 ', 'BGS', 12],
    ['  Bgs 12  ', 'BGS', 12],
    ['0012', null, 12],
    ['12', null, 12],
    ['#12', null, 12],
    ['#0012', null, 12],
    ['SLV-10000', 'SLV', 10000],
]);

test('TaskCodeQuery::parse() rejects what is not a code', function (string $typed) {
    expect(TaskCodeQuery::parse($typed))->toBeNull();
})->with(['AB-12', 'ABCD-12', '12a', '', '   ', 'BGS-', 'BGS', '#', '0', 'BGS-0', 'BG5-12', '1234567890', '#BGS-12', 'Fix invoice']);

// --- The Tasks page ------------------------------------------------------

test('every form of the code finds the task', function (string $typed) {
    expect(searchIds($this, $this->management, $this->bgs, ['q' => $typed]))->toContain($this->task12->id);
})->with(['BGS-0012', 'bgs-12', 'bgs12', 'BGS 12', 'bgs-0012 ', '0012', '12', '#12']);

test('"12" finds task 12 and a title containing 12, but not task 112', function () {
    expect(searchIds($this, $this->management, $this->bgs, ['q' => '12']))
        ->toBe(collect([$this->task12->id, $this->q12->id])->sort()->values()->all());
});

test('"BGS-0012" finds only task 12, and a title search still works', function () {
    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0012']))->toBe([$this->task12->id])
        ->and(searchIds($this, $this->management, $this->bgs, ['q' => 'invoice']))->toBe([$this->task12->id]);
});

test('a code no company has gets no code match, and the title search still runs', function () {
    $named = searchTask($this, 'bgs', null, 'Plan ABC-12 rollout');

    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'ABC-12']))->toBe([$named->id]);
});

test('another company\'s code lists nothing here, with a notice linking there when the user can see that task', function () {
    $slvTask = searchTask($this, 'slv', 3, 'Platform audit');

    $response = $this->actingAs($this->management)->get(route('tasks.index', [$this->bgs, 'q' => 'SLV-0003']))->assertOk();

    expect(collect($response->viewData('tasks')->items())->pluck('id')->all())->toBe([]);
    $response->assertSee('<a href="'.e(route('tasks.index', [$this->slv, 'q' => 'SLV-0003'])).'" class="font-medium hover:underline">SLV-0003 is in Solava.</a>', false);

    // Following it finds the task there.
    expect(searchIds($this, $this->management, $this->slv, ['q' => 'SLV-0003']))->toBe([$slvTask->id]);
});

test('no notice when the user can\'t see that company, or that task, or it doesn\'t exist', function () {
    searchTask($this, 'slv', 3, 'Platform audit');

    // Management of BGS only: Solava isn't one of their companies.
    $bgsOnly = User::factory()->create();
    OrgMember::create(['organization_id' => $this->bgs->id, 'user_id' => $bgsOnly->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    $this->actingAs($bgsOnly)->get(route('tasks.index', [$this->bgs, 'q' => 'SLV-0003']))->assertOk()->assertDontSee('is in Solava');

    // Staff in both companies, with no access to Solava's department.
    $staff = User::factory()->create();
    foreach ([$this->bgs, $this->slv] as $organization) {
        OrgMember::create(['organization_id' => $organization->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    }
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->bgs->id, 'department_id' => $this->bgsDept->id, 'allowed' => true]);
    $this->actingAs($staff)->get(route('tasks.index', [$this->bgs, 'q' => 'SLV-0003']))->assertOk()->assertDontSee('is in Solava');

    // A number Solava never issued.
    $this->actingAs($this->management)->get(route('tasks.index', [$this->bgs, 'q' => 'SLV-0999']))->assertOk()->assertDontSee('is in Solava');
});

test('a task the user can\'t see is never found by its code', function () {
    $otherDept = Department::create(['organization_id' => $this->bgs->id, 'name' => 'Finance', 'color' => '#000000']);
    $hidden = searchTask($this, 'bgs', 40, 'Payroll run', ['department_id' => $otherDept->id]);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->bgs->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->bgs->id, 'department_id' => $this->bgsDept->id, 'allowed' => true]);

    foreach (['BGS-0040', '40', '#40'] as $typed) {
        expect(searchIds($this, $staff, $this->bgs, ['q' => $typed]))->not->toContain($hidden->id);
    }
    // While management finds it.
    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0040']))->toBe([$hidden->id]);
});

test('an exact code finds an old completed task the completed window hides; its title alone does not', function () {
    $old = searchTask($this, 'bgs', 50, 'Archived supplier list', ['status' => 'completed']);
    DB::table('tasks')->where('id', $old->id)->update(['completed_at' => now()->subDays(60)]);

    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0050']))->toBe([$old->id])
        ->and(searchIds($this, $this->management, $this->bgs, ['q' => '#50']))->toBe([$old->id])
        ->and(searchIds($this, $this->management, $this->bgs, ['q' => 'Archived supplier']))->toBe([]);
});

test('a deactivated task is found by its code only with "show inactive"', function () {
    $inactive = searchTask($this, 'bgs', 60, 'Retired process');
    $inactive->delete();

    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0060']))->toBe([])
        ->and(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0060', 'show_inactive' => 1]))->toBe([$inactive->id]);
});

test('a task moved to another company is found by its new code only', function () {
    $this->task12->update([
        'organization_id' => $this->slv->id, 'project_id' => $this->slvProject->id, 'department_id' => $this->slvDept->id,
    ]);
    $newCode = $this->task12->fresh()->code;

    expect(searchIds($this, $this->management, $this->bgs, ['q' => 'BGS-0012']))->toBe([])
        ->and(searchIds($this, $this->management, $this->slv, ['q' => $newCode]))->toBe([$this->task12->id]);
});

test('the search box says it searches titles and codes', function () {
    $this->actingAs($this->management)->get(route('tasks.index', $this->bgs))->assertOk()
        ->assertSee('placeholder="Search title or code…"', false);
});
