<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * TC-55 (task #70): the add_edit_own_comment permission was enforced on
 * editing and deleting a comment (CommentPolicy::update()/delete(), via
 * canEditOwnComments()) but NOT on creating one — CommentPolicy::create()
 * took no Task and returned an unconditional true, so revoking the
 * permission from the Staff role correctly hid Edit/Delete while leaving
 * posting comments and replies fully working, server-side and in the UI.
 *
 * These cover both halves of that: the server now refuses (here and on the
 * five comment-context upload endpoints, which share the same gate), and
 * the view no longer renders a composer or Reply button the server would
 * then reject.
 *
 * Reactions are deliberately NOT gated by this permission — they require
 * only view access to the task (CommentReactionController authorizes
 * `view` on the task alone). Asserted below so that stays a decision
 * rather than an accident.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->staff = makeCommentPermissionStaff($this->org, $this->dept, $this->project);

    // An existing comment by someone else, so there's something to reply
    // to, react to, and read.
    $this->existing = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->owner->id,
        'body' => 'Original comment',
    ]);
});

function makeCommentPermissionStaff(Organization $org, Department $department, Project $project): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);
    $project->staff()->attach($staff->id);

    return $staff;
}

/** Revokes add_edit_own_comment from the Staff role, as the Permissions screen does. */
function revokeCommentPermissionFromStaff(): void
{
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $permissionId = Permission::where('slug', 'add_edit_own_comment')->firstOrFail()->id;
    $staffRole->permissions()->detach($permissionId);
}

test('TC-55: staff without add_edit_own_comment cannot post a comment', function () {
    revokeCommentPermissionFromStaff();

    $response = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Should be refused</p>',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('comments', ['user_id' => $this->staff->id]);
});

test('TC-55: staff without add_edit_own_comment cannot post a reply', function () {
    revokeCommentPermissionFromStaff();

    $response = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Should be refused</p>',
        'parent_comment_id' => $this->existing->id,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('comments', ['parent_comment_id' => $this->existing->id]);
});

/**
 * Counts REAL elements, not raw substrings: every one of these class names
 * also appears inside this view's own inline <script> (as a querySelector
 * argument, or the Reply button's own JS-built markup), so a
 * toContain()/not->toContain() on the page source would match the script
 * text and pass vacuously in both directions. Script contents are text
 * nodes, never elements, so an XPath element query ignores them.
 *
 * @return array{post: int, editor: int, reply: int, note: int}
 */
function commentComposerElementCounts(string $html): array
{
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    return [
        'post' => $xpath->query("//button[contains(concat(' ', @class, ' '), ' post-comment-btn ')]")->length,
        'editor' => $xpath->query("//*[contains(concat(' ', @class, ' '), ' new-comment-editor ')]")->length,
        'reply' => $xpath->query("//button[contains(concat(' ', @class, ' '), ' reply-comment-btn ')]")->length,
        'note' => $xpath->query("//*[contains(concat(' ', @class, ' '), ' comment-no-permission ')]")->length,
    ];
}

test('TC-55: the composer, Post button and Reply buttons are not rendered without the permission', function () {
    revokeCommentPermissionFromStaff();

    $response = $this->actingAs($this->staff)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    $counts = commentComposerElementCounts($response->getContent());

    expect($counts['post'])->toBe(0)
        ->and($counts['editor'])->toBe(0)
        ->and($counts['reply'])->toBe(0)
        ->and($counts['note'])->toBe(1);
    expect($response->getContent())->toContain("You don't have permission to comment.");
});

test('TC-55: the composer and Reply buttons ARE rendered with the permission', function () {
    $response = $this->actingAs($this->staff)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    $counts = commentComposerElementCounts($response->getContent());

    // One Reply button for the single existing comment in this fixture.
    expect($counts['post'])->toBe(1)
        ->and($counts['editor'])->toBe(1)
        ->and($counts['reply'])->toBe(1)
        ->and($counts['note'])->toBe(0);
});

test('TC-55: staff without the permission can still read existing comments', function () {
    revokeCommentPermissionFromStaff();

    $response = $this->actingAs($this->staff)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    expect($response->getContent())->toContain('Original comment');

    // The polling endpoint stays available too — losing the ability to
    // comment must not break reading the thread.
    $this->actingAs($this->staff)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
});

test('TC-55: staff without the permission can still react (reactions need view access only)', function () {
    revokeCommentPermissionFromStaff();

    $response = $this->actingAs($this->staff)->postJson("/comments/{$this->existing->id}/reactions", [
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('comment_reactions', [
        'comment_id' => $this->existing->id,
        'user_id' => $this->staff->id,
    ]);
});

test('TC-55: staff WITH the permission can still post a comment and a reply', function () {
    $comment = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Allowed</p>',
    ]);
    $comment->assertCreated();

    $reply = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Allowed reply</p>',
        'parent_comment_id' => $this->existing->id,
    ]);
    $reply->assertCreated();

    $this->assertDatabaseHas('comments', ['user_id' => $this->staff->id, 'parent_comment_id' => $this->existing->id]);
});

test('TC-55: owner can still post even after the Staff role loses the permission', function () {
    revokeCommentPermissionFromStaff();

    $this->actingAs($this->owner)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Owner comment</p>',
    ])->assertCreated();
});

test('TC-55: super_admin can still post even after the Staff role loses the permission', function () {
    revokeCommentPermissionFromStaff();

    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    $this->actingAs($superAdmin)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Super admin comment</p>',
    ])->assertCreated();
});

/**
 * The five comment-context upload/preview endpoints share CommentPolicy
 *
 * @create with the comment endpoint itself (each already called it — it
 * was simply a no-op before this fix), so revoking the permission has to
 * close all of them, not just the POST that creates the comment row.
 */
test('TC-55: comment-context uploads are refused without the permission', function (string $route, string $kind) {
    Storage::fake(config('filestorage.disk'));
    revokeCommentPermissionFromStaff();

    // Real payloads, not placeholders: each endpoint validates BEFORE it
    // authorizes, so an invalid body would 422 and never reach the gate
    // this test exists to exercise.
    $payload = match ($kind) {
        'image' => ['file' => UploadedFile::fake()->image('shot.png')],
        'audio' => ['file' => UploadedFile::fake()->create('clip.mp3', 16, 'audio/mpeg')],
        'video' => ['file' => UploadedFile::fake()->create('clip.mp4', 16, 'video/mp4')],
        'document' => ['file' => UploadedFile::fake()->create('notes.pdf', 16, 'application/pdf')],
        'link' => ['url' => 'https://example.com'],
    };

    $response = $this->actingAs($this->staff)
        ->post("/tasks/{$this->task->id}/{$route}", $payload + ['context' => 'comment']);

    $response->assertForbidden();
})->with([
    'image' => ['images', 'image'],
    'audio' => ['audio', 'audio'],
    'video' => ['video', 'video'],
    'document' => ['document-uploads', 'document'],
    'link preview' => ['link-previews', 'link'],
]);
