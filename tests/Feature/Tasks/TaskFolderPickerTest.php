<?php

use App\Models\Department;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Covers task #73 phase 4's folder-attach picker list endpoint
 * (GET /tasks/{task}/folders/attachable) — same gate as the document
 * picker (TaskPolicy::attachDocuments(), reused verbatim), folders
 * restricted to the task's own company via DocumentFolder::
 * scopeAttachableTo(), excluding already-linked folders, searched and
 * paginated.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Task', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

function makePickerFolder(Organization $org, User $creator, string $name, ?int $parentId = null): DocumentFolder
{
    return DocumentFolder::create([
        'organization_id' => $org->id, 'parent_id' => $parentId, 'name' => $name, 'created_by' => $creator->id,
    ]);
}

test('lists folders in the task\'s company', function () {
    $folder = makePickerFolder($this->orgA, $this->management, 'Marketing');

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable");

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('id'))->toContain($folder->id);
});

test('never returns a folder from another company', function () {
    $inOrgB = makePickerFolder($this->orgB, $this->owner, 'Org B folder');

    $response = $this->actingAs($this->owner)->getJson("/tasks/{$this->task->id}/folders/attachable");

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('id'))->not->toContain($inOrgB->id);
});

test('excludes a folder already linked to this task', function () {
    $linked = makePickerFolder($this->orgA, $this->management, 'Already linked');
    $notLinked = makePickerFolder($this->orgA, $this->management, 'Not linked');
    $this->task->folders()->attach($linked->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable");

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->not->toContain($linked->id);
    expect($ids)->toContain($notLinked->id);
});

test('search matches folder name, case-insensitively', function () {
    $match = makePickerFolder($this->orgA, $this->management, 'Vendor Agreements');
    $noMatch = makePickerFolder($this->orgA, $this->management, 'Unrelated');

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable?search=vendor");

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id);
    expect($ids)->not->toContain($noMatch->id);
});

test('each result carries a breadcrumb-style path, not just the folder\'s own name', function () {
    $parent = makePickerFolder($this->orgA, $this->management, 'Marketing');
    $child = makePickerFolder($this->orgA, $this->management, 'Q3', $parent->id);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable");

    $response->assertOk();
    $byId = collect($response->json('data'))->keyBy('id');
    expect($byId[$child->id]['path'])->toBe('Marketing / Q3');
    expect($byId[$parent->id]['path'])->toBe('Marketing');
});

test('pagination returns 25 per page with more available on the next page', function () {
    collect(range(1, 30))->each(fn (int $i) => makePickerFolder($this->orgA, $this->management, "Folder {$i}"));

    $page1 = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable");
    $page1->assertOk();
    expect($page1->json('data'))->toHaveCount(25);
    expect($page1->json('total'))->toBe(30);

    $page2 = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable?page=2");
    $page2->assertOk();
    expect($page2->json('data'))->toHaveCount(5);
});

test('manage_documents OFF 403s the picker list endpoint for staff', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/folders/attachable")->assertForbidden();
});
