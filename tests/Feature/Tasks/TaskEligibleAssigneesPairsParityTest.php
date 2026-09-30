<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * task #70 (code-review follow-up): Task::eligibleAssigneesForPairs() is a
 * batched, no-N+1 stand-in for calling eligibleAssigneesFor() once per
 * (project, department) pair - this is the regression guard asserting the
 * two NEVER disagree, across multiple organizations/projects/departments
 * in a single call, mirroring TaskViewableIdsParityTest's own precedent for
 * viewableIdsFor() vs. Gate::allows('view', ...).
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptA1 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptA2 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Sales', 'color' => '#111111']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#222222']);
    $this->deptB1 = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Ops', 'color' => '#333333']);
    $this->projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd']);
});

function makeStaffForPairsParity(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    return $staff;
}

/** Asserts eligibleAssigneesForPairs() agrees with eligibleAssigneesFor() called individually, for every given pair. */
function assertEligibleAssigneesPairsParity(Collection $pairs): void
{
    $batched = Task::eligibleAssigneesForPairs($pairs);

    foreach ($pairs as $pair) {
        $project = $pair['project'];
        $departmentId = $pair['departmentId'];

        $individual = Task::eligibleAssigneesFor($project, $departmentId)->pluck('id')->sort()->values()->all();
        $fromBatch = collect($batched[$project->id][$departmentId] ?? [])->pluck('id')->sort()->values()->all();

        expect($fromBatch)->toBe($individual, "Mismatch for project {$project->id}, department {$departmentId}");
    }
}

test('agrees with the individual method across multiple projects and departments within one organization', function () {
    $eligible = makeStaffForPairsParity($this->orgA);
    $this->projectA->staff()->attach($eligible->id);
    AccessPermission::create(['user_id' => $eligible->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->deptA1->id, 'allowed' => true]);

    $ineligible = makeStaffForPairsParity($this->orgA);
    $this->projectA->staff()->attach($ineligible->id);
    // No department access granted at all.

    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $pairs = collect([
        ['project' => $this->projectA, 'departmentId' => $this->deptA1->id],
        ['project' => $this->projectA, 'departmentId' => $this->deptA2->id],
    ]);

    assertEligibleAssigneesPairsParity($pairs);

    $deptA1Ids = collect(Task::eligibleAssigneesForPairs($pairs)[$this->projectA->id][$this->deptA1->id])->pluck('id')->all();
    expect($deptA1Ids)->toContain($eligible->id, $management->id, $this->owner->id)->not->toContain($ineligible->id);
});

test('agrees with the individual method across multiple organizations in one call', function () {
    $staffA = makeStaffForPairsParity($this->orgA);
    $this->projectA->staff()->attach($staffA->id);
    AccessPermission::create(['user_id' => $staffA->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->deptA1->id, 'allowed' => true]);

    $staffB = makeStaffForPairsParity($this->orgB);
    $this->projectB->staff()->attach($staffB->id);
    AccessPermission::create(['user_id' => $staffB->id, 'organization_id' => $this->orgB->id, 'department_id' => $this->deptB1->id, 'allowed' => true]);

    $pairs = collect([
        ['project' => $this->projectA, 'departmentId' => $this->deptA1->id],
        ['project' => $this->projectB, 'departmentId' => $this->deptB1->id],
    ]);

    assertEligibleAssigneesPairsParity($pairs);

    $orgAIds = collect(Task::eligibleAssigneesForPairs($pairs)[$this->projectA->id][$this->deptA1->id])->pluck('id')->all();
    $orgBIds = collect(Task::eligibleAssigneesForPairs($pairs)[$this->projectB->id][$this->deptB1->id])->pluck('id')->all();
    expect($orgAIds)->toContain($staffA->id)->not->toContain($staffB->id);
    expect($orgBIds)->toContain($staffB->id)->not->toContain($staffA->id);
});

test('agrees with the individual method for a deactivated department', function () {
    $staff = makeStaffForPairsParity($this->orgA);
    $this->projectA->staff()->attach($staff->id);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->deptA1->id, 'allowed' => true]);
    $this->deptA1->update(['is_active' => false]);

    $pairs = collect([['project' => $this->projectA, 'departmentId' => $this->deptA1->id]]);

    assertEligibleAssigneesPairsParity($pairs);

    $ids = collect(Task::eligibleAssigneesForPairs($pairs)[$this->projectA->id][$this->deptA1->id])->pluck('id')->all();
    expect($ids)->not->toContain($staff->id);
});

test('deduplicates identical pairs passed more than once', function () {
    $pairs = collect([
        ['project' => $this->projectA, 'departmentId' => $this->deptA1->id],
        ['project' => $this->projectA, 'departmentId' => $this->deptA1->id],
    ]);

    $result = Task::eligibleAssigneesForPairs($pairs);

    expect($result)->toHaveCount(1);
    expect($result[$this->projectA->id])->toHaveCount(1);
});

test('returns an empty array for an empty input without querying anything', function () {
    expect(Task::eligibleAssigneesForPairs(collect()))->toBe([]);
});
