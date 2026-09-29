<?php

use App\Models\Department;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Covers task #73 phase 4's "linked to N tasks" indicator on the
 * Documents page's folder rows — a plain count (see DocumentController::
 * index()'s own comment on why not the hover-popover treatment documents
 * already have), one grouped query for the whole page, not one per row.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

test('a folder linked to tasks shows the correct count on the Documents page', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => 'Launch assets', 'created_by' => $this->management->id]);
    $taskOne = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'One', 'priority' => 'medium', 'status' => 'pending']);
    $taskTwo = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'Two', 'priority' => 'medium', 'status' => 'pending']);
    $taskOne->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $taskTwo->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->org->id);

    $response->assertOk();
    expect($response->viewData('linkedFolderTaskCounts')[$folder->id])->toBe(2);
    $response->assertSee('Launch assets');
    $response->assertSeeInOrder(['Launch assets', '2']);
});

test('a folder linked to no tasks shows 0, not a hidden count', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => 'Unlinked', 'created_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->org->id);

    $response->assertOk();
    expect($response->viewData('linkedFolderTaskCounts')->has($folder->id))->toBeFalse();
});

test('the folder count does not grow the page\'s query count as more folders are added', function () {
    collect(range(1, 5))->each(function (int $i) {
        $folder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => "Folder {$i}", 'created_by' => $this->management->id]);
        $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => "T{$i}", 'priority' => 'medium', 'status' => 'pending']);
        $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    });

    DB::enableQueryLog();
    $this->actingAs($this->management)->get('/documents/'.$this->org->id)->assertOk();
    $groupedQueries = collect(DB::getQueryLog())->filter(fn ($query) => str_contains($query['query'], 'task_folder_links') && str_contains($query['query'], 'group by'));
    DB::flushQueryLog();

    expect($groupedQueries)->toHaveCount(1);
});
