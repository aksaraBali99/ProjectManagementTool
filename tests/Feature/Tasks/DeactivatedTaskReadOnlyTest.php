<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Support\DeactivatedTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * task #123: a deactivated (soft-deleted) task is archived. It can be
 * viewed, and the only action allowed on it is Activate. Every change to
 * it, its subtasks, comments, reactions and linked documents is refused by
 * its policy with DeactivatedTask::MESSAGE, on the page and on the server.
 */
beforeEach(function () {
    Storage::fake('r2');

    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    // The assignee: a staff member who can edit the task but not deactivate it.
    $this->assignee = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->assignee->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $this->assignee->id, 'organization_id' => $this->org->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $this->project->staff()->attach($this->assignee->id);

    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'assignee_id' => $this->assignee->id, 'title' => 'Archived task', 'description' => '<p>Old text</p>',
        'priority' => 'medium', 'status' => 'pending',
    ]);
    $this->subtask = Subtask::create(['task_id' => $this->task->id, 'title' => 'A subtask', 'is_done' => false]);
    $this->comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '<p>First comment</p>']);

    $this->document = Document::create([
        'organization_id' => $this->org->id, 'uploaded_by' => $this->management->id, 'name' => 'Spec.pdf',
        'link' => 'https://example.com/spec.pdf', 'access_level' => 'internal',
    ]);
    $this->linkedDocument = Document::create([
        'organization_id' => $this->org->id, 'uploaded_by' => $this->management->id, 'name' => 'Linked.pdf',
        'link' => 'https://example.com/linked.pdf', 'access_level' => 'internal',
    ]);
    $this->task->documents()->attach($this->linkedDocument->id);
    $this->folder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);
    $this->linkedFolder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => 'Linked folder', 'created_by' => $this->management->id]);
    $this->task->folders()->attach($this->linkedFolder->id, ['linked_by' => $this->management->id]);

    $this->task->delete();
});

function taskUpdateFields(object $context, array $overrides = []): array
{
    return array_merge([
        'project_id' => $context->project->id, 'department_id' => $context->dept->id,
        'title' => 'Changed title', 'priority' => 'high', 'status' => 'in_progress',
    ], $overrides);
}

/** A refused JSON request: 403 carrying the deactivated message. */
function assertRefused($response): void
{
    $response->assertForbidden()->assertJson(['message' => DeactivatedTask::MESSAGE]);
}

// --- The edit page -----------------------------------------------------------

test('management sees the notice, the read-only details and Activate, and no form', function () {
    $page = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk();

    $page->assertSee(DeactivatedTask::MESSAGE)
        ->assertSee('Archived task')
        ->assertSee('Activate task')
        ->assertDontSee('Save changes')
        ->assertDontSee('id="edit-task-form"', false)
        // The read-only block's line about toggling subtasks is wrong here.
        ->assertDontSee('You can view this task and toggle its subtasks');
    $html = $page->getContent();

    // Subtasks: shown, checkbox disabled, no add or delete controls.
    expect($html)->toContain('A subtask')
        ->toMatch('/class="subtask-toggle[^"]*"[^>]*disabled/')
        ->and($html)->toContain('data-can-edit="0"')
        // Comments: listed, but no composer, Reply, Edit, Delete or reactions.
        ->toContain('First comment')
        // Server-rendered buttons only (each on its own line); the comments
        // script also holds these as JS strings, for comments it adds.
        ->not->toMatch('/^\s*<button type="button" class="(reply|edit|delete|react)-comment-btn/m')
        ->toContain('const canComment = false;')
        ->toContain('const canReact = false;')
        // The unsaved-changes guard only comes with the form.
        ->not->toContain("getElementById('edit-task-form')");
});

test('the assignee sees the read-only page and the shorter notice, with no Activate button', function () {
    $page = $this->actingAs($this->assignee)->get(route('tasks.edit', $this->task))->assertOk();

    $page->assertSee('This task is deactivated.')
        ->assertDontSee(DeactivatedTask::MESSAGE)
        ->assertDontSee('Activate task')
        ->assertDontSee('Save changes');
});

test('an active task\'s edit page is unchanged: the form, Deactivate, no notice', function () {
    $this->task->restore();

    $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()
        ->assertSee('Save changes')
        ->assertSee('Deactivate task')
        ->assertDontSee('This task is deactivated');
});

test('the comments still load on a deactivated task, offering no edit', function () {
    $response = $this->actingAs($this->management)->getJson(route('comments.index', $this->task))->assertOk();

    expect($response->json('comments.0.body'))->toBe('<p>First comment</p>')
        ->and($response->json('comments.0.can_edit'))->toBeFalse();
});

// --- The task -----------------------------------------------------------------

test('saving the task is refused with the message, and nothing changes', function () {
    assertRefused($this->actingAs($this->management)->putJson("/tasks/{$this->task->id}", taskUpdateFields($this)));

    // A plain form submit (a tab left open) gets the 403 page, saying why.
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", taskUpdateFields($this))
        ->assertForbidden()
        ->assertSee(DeactivatedTask::MESSAGE);

    expect($this->task->fresh()->title)->toBe('Archived task')
        ->and($this->task->fresh()->status->value)->toBe('pending');
});

test('the assignee\'s save is refused too', function () {
    assertRefused($this->actingAs($this->assignee)->putJson("/tasks/{$this->task->id}", taskUpdateFields($this)));
    expect($this->task->fresh()->title)->toBe('Archived task');
});

test('changing the status or the assignee is refused', function () {
    assertRefused($this->actingAs($this->management)->patchJson("/tasks/{$this->task->id}/status", ['status' => 'completed']));
    assertRefused($this->actingAs($this->management)->patchJson("/tasks/{$this->task->id}/assignee", ['assignee_id' => null]));

    expect($this->task->fresh()->status->value)->toBe('pending')
        ->and($this->task->fresh()->assignee_id)->toBe($this->assignee->id);
});

// --- Subtasks -------------------------------------------------------------------

test('adding, ticking, editing and deleting a subtask are refused', function () {
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/subtasks", ['title' => 'New one']));
    assertRefused($this->actingAs($this->management)->patchJson("/subtasks/{$this->subtask->id}/toggle"));
    assertRefused($this->actingAs($this->management)->putJson("/subtasks/{$this->subtask->id}", ['title' => 'Renamed']));
    assertRefused($this->actingAs($this->management)->deleteJson("/subtasks/{$this->subtask->id}"));
    // The assignee can tick subtasks on an active task, but not here.
    assertRefused($this->actingAs($this->assignee)->patchJson("/subtasks/{$this->subtask->id}/toggle"));

    $subtask = $this->subtask->fresh();
    expect(Subtask::where('task_id', $this->task->id)->count())->toBe(1)
        ->and($subtask->title)->toBe('A subtask')
        ->and($subtask->is_done)->toBeFalse();
});

// --- Comments and reactions -------------------------------------------------------

test('adding, editing and deleting a comment and reacting are refused', function () {
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => '<p>New</p>']));
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => '<p>Reply</p>', 'parent_comment_id' => $this->comment->id]));
    assertRefused($this->actingAs($this->management)->putJson("/comments/{$this->comment->id}", ['body' => '<p>Edited</p>']));
    assertRefused($this->actingAs($this->management)->deleteJson("/comments/{$this->comment->id}"));
    assertRefused($this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍']));

    expect(Comment::where('task_id', $this->task->id)->count())->toBe(1)
        ->and($this->comment->fresh()->body)->toBe('<p>First comment</p>')
        ->and(DB::table('comment_reactions')->count())->toBe(0);
});

test('removing an existing reaction is refused too', function () {
    DB::table('comment_reactions')->insert(['comment_id' => $this->comment->id, 'user_id' => $this->management->id, 'emoji' => '👍', 'created_at' => now()]);

    // The same endpoint toggles: posting the same emoji again removes it.
    assertRefused($this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍']));

    expect(DB::table('comment_reactions')->count())->toBe(1);
});

// --- Documents and folders ------------------------------------------------------------

test('attaching and detaching documents and folders are refused, and the links stay', function () {
    assertRefused($this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable"));
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $this->document->id]));
    assertRefused($this->actingAs($this->management)->deleteJson("/tasks/{$this->task->id}/documents/{$this->linkedDocument->id}"));
    assertRefused($this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/attachable"));
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $this->folder->id]));
    assertRefused($this->actingAs($this->management)->deleteJson("/tasks/{$this->task->id}/folders/{$this->linkedFolder->id}"));

    $task = Task::withTrashed()->findOrFail($this->task->id);
    expect($task->documents()->pluck('documents.id')->all())->toBe([$this->linkedDocument->id])
        ->and($task->folders()->pluck('document_folders.id')->all())->toBe([$this->linkedFolder->id]);
});

test('creating a new document attached to a deactivated task is refused, and no document is made', function () {
    $before = Document::count();

    assertRefused($this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id, 'task_id' => $this->task->id, 'name' => 'New link',
        'link' => 'https://example.com/new', 'access_level' => 'internal',
    ]));

    expect(Document::count())->toBe($before);
});

test('a linked folder can still be opened on a deactivated task', function () {
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/folders/{$this->linkedFolder->id}/expand")
        ->assertOk();
});

test('opening a linked folder still needs the task to be viewable', function () {
    // A staff member of the company with no access to the task's department.
    $outsider = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $outsider->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    $status = $this->actingAs($outsider)
        ->getJson("/tasks/{$this->task->id}/folders/{$this->linkedFolder->id}/expand")
        ->getStatusCode();

    // Refused before anything about the folder is returned (the task's
    // company scope may hide it entirely: 404).
    expect($status)->toBeIn([403, 404]);
});

// --- Rich-text uploads -------------------------------------------------------------------

test('uploading an image for the description or a comment is refused, and no file is stored', function (string $context) {
    assertRefused($this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('picture.png', 200, 200),
        'context' => $context,
    ]));

    expect(Storage::disk('r2')->allFiles())->toBe([]);
})->with(['description', 'comment']);

// --- Activate ------------------------------------------------------------------------------

test('Activate still works, and afterwards the task, its subtasks and comments can be changed again', function () {
    $this->actingAs($this->management)->patch("/tasks/{$this->task->id}/toggle-active")
        ->assertRedirect(route('tasks.edit', $this->task))
        ->assertSessionHas('status', 'Task activated.');
    expect($this->task->fresh()->trashed())->toBeFalse();

    $this->actingAs($this->management)->putJson("/tasks/{$this->task->id}", taskUpdateFields($this))->assertRedirect();
    expect($this->task->fresh()->title)->toBe('Changed title');

    $this->actingAs($this->management)->patchJson("/subtasks/{$this->subtask->id}/toggle")->assertOk();
    expect($this->subtask->fresh()->is_done)->toBeTrue();

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => '<p>Back again</p>'])->assertCreated();
    expect(Comment::where('task_id', $this->task->id)->count())->toBe(2);

    $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()
        ->assertSee('Save changes')
        ->assertDontSee('This task is deactivated');
});

test('Activate from the edit page keeps the back link (task #121)', function () {
    $kanban = ['return_to' => url('/kanban/'.$this->org->id), 'return_label' => 'Kanban'];

    $response = $this->actingAs($this->management)->patch("/tasks/{$this->task->id}/toggle-active", $kanban);

    $response->assertRedirect(route('tasks.edit', ['task' => $this->task, ...$kanban]));
    $this->get($response->headers->get('Location'))->assertSee('← Kanban')->assertSee('Save changes');
});

test('the assignee can\'t activate (unchanged: management and above only)', function () {
    $this->actingAs($this->assignee)->patch("/tasks/{$this->task->id}/toggle-active")->assertForbidden();
    expect($this->task->fresh()->trashed())->toBeTrue();
});

// --- Tasks page access (TaskPolicy::viewAny) -----------------------------------------------

test('a subtask on a deactivated task doesn\'t open its company\'s Tasks page', function () {
    // A staff member of the company with no department access, whose only
    // link to it is one subtask assignment.
    $helper = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $helper->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    $this->subtask->update(['assignee_id' => $helper->id]);

    // Deactivated task: no access.
    $this->actingAs($helper)->get(route('tasks.index', $this->org->id))->assertForbidden();

    // The same subtask on an active task does give access, so the line
    // above tests the deactivation, not a setup gap.
    $this->task->restore();
    $this->actingAs($helper)->get(route('tasks.index', $this->org->id))->assertOk();
});
