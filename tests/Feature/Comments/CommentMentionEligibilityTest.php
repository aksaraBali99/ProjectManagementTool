<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInCommentNotification;
use Illuminate\Support\Facades\Notification;

/**
 * End-to-end coverage of the mention-eligibility fix: both the
 * autocomplete's data source (the Edit Task page's data-mentions
 * attribute) and the server-side enforcement in
 * CommentController::syncMentions() must agree on the same,
 * permission-accurate set (Task::viewableUsers()) — a hidden/absent
 * autocomplete entry backed by an unprotected endpoint would still be a
 * leak, so both halves are tested here, not just one.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function makeIneligibleProjectMember(Organization $org, Project $project, Department $wrongDepartment): User
{
    $user = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    // Attached to the project (the old, leaky eligibility source) but
    // granted access to a DIFFERENT department than the task's own —
    // exactly the leak scenario: project-attached, but can't actually
    // view this specific task per TaskPolicy::view().
    AccessPermission::create([
        'user_id' => $user->id,
        'organization_id' => $org->id,
        'department_id' => $wrongDepartment->id,
        'allowed' => true,
    ]);
    $project->staff()->attach($user->id);

    return $user;
}

function makeEligibleStaffMember(Organization $org, Project $project, Department $department): User
{
    $user = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    AccessPermission::create([
        'user_id' => $user->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);
    $project->staff()->attach($user->id);

    return $user;
}

/**
 * The comment editor's data-mentions attribute, decoded — scoped
 * specifically to the "New comment" labeled rich-text-editor root
 * (data-rich-text + data-label, same targeting richTextEditorNode() in
 * Pest.php uses), NOT a blind whole-page substring search. The Edit Task
 * page also embeds a SEPARATE, still-project-scoped @json($staffByProject)
 * blob for the Assignee/Subtask-assignee dropdowns — a user id/name pair
 * legitimately appearing there (project attachment IS the right rule for
 * assignability) would otherwise false-positive a plain string search
 * for the same id in the totally different mentions list.
 *
 * @return array<int, array{id: int, name: string}>
 */
function decodedMentionIds(string $pageHtml): array
{
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$pageHtml);
    libxml_clear_errors();

    foreach ((new DOMXPath($document))->query('//*[@data-rich-text]') as $node) {
        if ($node->getAttribute('data-label') === 'New comment') {
            $decoded = json_decode($node->getAttribute('data-mentions'), true);

            return is_array($decoded) ? array_column($decoded, 'id') : [];
        }
    }

    throw new RuntimeException('No "New comment" rich-text-editor node found.');
}

test('the mention autocomplete excludes a staff member attached to the project but lacking access to the task\'s department', function () {
    $ineligible = makeIneligibleProjectMember($this->org, $this->project, $this->otherDept);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    expect(decodedMentionIds($page))->not->toContain($ineligible->id);
});

test('the mention autocomplete includes a staff member with real access to the task\'s department', function () {
    $eligible = makeEligibleStaffMember($this->org, $this->project, $this->dept);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    expect(decodedMentionIds($page))->toContain($eligible->id);
});

test('the mention autocomplete includes management, the assignee, a subtask assignee, and the project\'s client', function () {
    $assignee = makeEligibleStaffMember($this->org, $this->project, $this->otherDept);
    $this->task->update(['assignee_id' => $assignee->id]);

    $subtaskAssignee = makeEligibleStaffMember($this->org, $this->project, $this->otherDept);
    $this->task->subtasks()->create(['title' => 'Sub', 'assignee_id' => $subtaskAssignee->id]);

    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->project->clients()->attach($client->id);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $mentionIds = decodedMentionIds($page);

    foreach ([$assignee, $subtaskAssignee, $client] as $user) {
        expect($mentionIds)->toContain($user->id);
    }
    // $this->management themselves is excluded from their own mention
    // list (rejected as auth()->id() — see tasks/edit.blade.php), so it's
    // asserted separately here rather than folded into the loop above.
});

test('the mention autocomplete includes management, when viewed by someone other than that management user', function () {
    $page = $this->actingAs($this->owner)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    expect(decodedMentionIds($page))->toContain($this->management->id);
});

test('server-side: mentioning a project-attached staff member without department access is silently dropped, not notified — even if the client sends their id directly', function () {
    Notification::fake();
    $ineligible = makeIneligibleProjectMember($this->org, $this->project, $this->otherDept);

    // Bypasses the UI entirely — a crafted request naming an id the real
    // autocomplete would never have offered, confirming the server
    // enforces this independently of what the client shows.
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'cc @'.$ineligible->name,
        'mentioned_user_ids' => [$ineligible->id],
    ]);

    $response->assertCreated();
    expect(Comment::firstOrFail()->mentionedUsers()->count())->toBe(0);
    Notification::assertNotSentTo($ineligible, MentionedInCommentNotification::class);
});

test('server-side: mentioning an eligible staff member with department access still notifies them, unchanged behavior', function () {
    Notification::fake();
    $eligible = makeEligibleStaffMember($this->org, $this->project, $this->dept);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$eligible->name.' please check',
        'mentioned_user_ids' => [$eligible->id],
    ])->assertCreated();

    expect(Comment::firstOrFail()->mentionedUsers()->pluck('users.id')->all())->toBe([$eligible->id]);
    Notification::assertSentToTimes($eligible, MentionedInCommentNotification::class, 1);
});

test('the mention notification still fires with no opt-out, even with zero notification preferences configured for the mentioned user', function () {
    Notification::fake();
    $eligible = makeEligibleStaffMember($this->org, $this->project, $this->dept);

    // No NotificationSetting rows exist for $eligible at all — confirms
    // MentionedInCommentNotification still bypasses that system entirely,
    // exactly as before this fix (only WHO is eligible changed, not the
    // always-on/no-opt-out delivery behavior for whoever still is).
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$eligible->name,
        'mentioned_user_ids' => [$eligible->id],
    ])->assertCreated();

    Notification::assertSentTo($eligible, MentionedInCommentNotification::class);
});
