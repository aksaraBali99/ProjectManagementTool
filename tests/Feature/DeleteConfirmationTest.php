<?php

use App\Enums\DocumentAccessLevel;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Document;
use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

/**
 * A regression guard for every one of the app's 4 DELETE-capable UI
 * actions (confirmed by grepping every Route::delete in routes/web.php):
 * subtask delete, comment delete, task-document detach, and notification
 * rule delete. Each used to fire its destructive request immediately on
 * click/submit, with no confirmation, so one accidental click permanently
 * lost a comment/subtask (or, for detach, at least unlinked a document
 * without warning).
 *
 * Pest can't drive an actual browser confirm() dialog (no JS test runner
 * in this project - see package.json - and confirm() runs entirely
 * client-side before the request is even sent, so an HTTP test can't
 * observe it behaviorally either way). What these tests CAN and do check
 * is that the confirm() guard is actually present in the shipped
 * page/script source, as a regression guard against it being silently
 * removed later. The real interactive behavior was verified manually.
 */
beforeEach(function () {
    $this->owner = createOwner();

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

test('the Edit Task page confirms before deleting a subtask', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Subtask::create(['task_id' => $task->id, 'title' => 'A subtask']);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee("if (! confirm('Delete this subtask? This cannot be undone.')) return;", false);
});

test('the Edit Task page confirms before deleting a comment', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Comment::create(['task_id' => $task->id, 'user_id' => $this->management->id, 'body' => 'A comment']);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee("if (! confirm('Delete this comment? This cannot be undone.')) return;", false);
});

test('the Edit Task page confirms before detaching a document', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Handbook.pdf',
        'link' => 'https://example.com/handbook.pdf',
        'access_level' => DocumentAccessLevel::Internal,
    ]);
    $task->documents()->attach($document->id);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee('if (! confirm(\'Remove this document from the task? It will stay in the company\\\'s document library.\')) return;', false);
});

test('the Notification Settings page confirms before deleting a team rule', function () {
    NotificationSetting::create([
        'owner_id' => $this->owner->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->management->id]],
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->owner)->get('/notification-settings');

    $response->assertOk();
    $response->assertSee("onsubmit=\"return confirm('Delete this notification rule? This cannot be undone.');\"", false);
});
