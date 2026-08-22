<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeTaskForStartDateTest(Project $project, Department $department, string $status = 'pending'): Task
{
    return Task::create([
        'organization_id' => $project->organization_id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => $status,
    ]);
}

test('a task created as pending has no start_date', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);

    expect($task->start_date)->toBeNull();
});

test('moving a task to in_progress sets start_date to today', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);

    expect($task->fresh()->start_date->toDateString())->toBe(now()->toDateString());
});

test('moving to in_progress does not overwrite an already-set start_date', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);
    $task->update(['start_date' => '2020-01-01']);

    // The real edit form always round-trips the field's current value (it's
    // pre-filled from $task->start_date), so this mirrors that rather than
    // omitting it — omitting it entirely is itself a clear (matching
    // due_date's identical, pre-existing convention), not a "leave as-is".
    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => 'in_progress',
        'start_date' => '2020-01-01',
    ]);

    expect($task->fresh()->start_date->toDateString())->toBe('2020-01-01');
});

test('omitting start_date entirely from an update clears it, matching due_date\'s existing convention', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);
    $task->update(['start_date' => '2020-01-01', 'status' => 'in_progress']);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);

    expect($task->fresh()->start_date)->toBeNull();
});

test('start_date is manually editable via the task edit form', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => 'pending',
        'start_date' => '2026-01-15',
    ]);

    expect($task->fresh()->start_date->toDateString())->toBe('2026-01-15');
});

test('moving via the kanban status endpoint also sets start_date', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);

    $this->actingAs($this->management)
        ->patchJson("/tasks/{$task->id}/status", ['status' => 'in_progress'])
        ->assertOk();

    expect($task->fresh()->start_date->toDateString())->toBe(now()->toDateString());
});

test('moving a task to in_progress cascades start_date to subtasks that have none yet', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);
    $withDate = $task->subtasks()->create(['title' => 'Already started', 'start_date' => '2020-01-01']);
    $withoutDate = $task->subtasks()->create(['title' => 'Not started yet']);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);

    expect($withDate->fresh()->start_date->toDateString())->toBe('2020-01-01')
        ->and($withoutDate->fresh()->start_date->toDateString())->toBe(now()->toDateString());
});

test('a subtask start_date is manually editable', function () {
    $task = makeTaskForStartDateTest($this->projectA, $this->deptA);
    $subtask = $task->subtasks()->create(['title' => 'Sub']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['start_date' => '2026-02-01'])
        ->assertOk();

    expect($subtask->fresh()->start_date->toDateString())->toBe('2026-02-01');
});
