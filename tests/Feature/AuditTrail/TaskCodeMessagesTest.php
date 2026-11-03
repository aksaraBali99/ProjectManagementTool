<?php

use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AuditEventDatabaseNotification;
use App\Notifications\AuditEventMailNotification;
use App\Notifications\MentionedInCommentNotification;
use Illuminate\Support\Facades\DB;

/**
 * task #118 phase 4: every message about a task names it by code and
 * title — AuditLog::entityLabel() (the Audit Trail, in-app notifications
 * and email), the @mention notification, and the Audit Trail's own
 * change text for task.created's number.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Bali Goods Sourcing', 'slug' => 'bgs', 'code' => 'BGS', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Ops', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Sourcing', 'description' => 'd']);
    $this->staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    $this->project->staff()->attach($this->staff->id);

    $this->actingAs($this->owner);
    $this->task = messageTask($this, 'Fix invoice PDF layout');
});

function messageTask(object $context, string $title, array $attributes = []): Task
{
    return Task::create(array_merge([
        'organization_id' => $context->org->id, 'project_id' => $context->project->id, 'department_id' => $context->dept->id,
        'assignee_id' => $context->staff->id, 'title' => $title, 'priority' => 'medium', 'status' => 'pending',
    ], $attributes));
}

function latestEntry(string $action): AuditLog
{
    return AuditLog::where('action', $action)->latest('id')->firstOrFail();
}

// --- entityLabel() -------------------------------------------------------

test('a task is named by code and title', function () {
    expect(latestEntry('task.created')->entityLabel())->toBe('BGS-0001 Fix invoice PDF layout');
});

test('a subtask is named by its parent and itself, and keeps the old text once deleted', function () {
    $subtask = Subtask::create(['task_id' => $this->task->id, 'title' => 'Write tests']);
    $entry = latestEntry('subtask.created');

    expect($entry->entityLabel())->toBe('BGS-0001 Fix invoice PDF layout › Write tests');

    // The parent deactivated: still named.
    $this->task->delete();
    expect($entry->fresh()->entityLabel())->toBe('BGS-0001 Fix invoice PDF layout › Write tests');

    // Hard-deleted subtask: the existing placeholder.
    $subtask->delete();
    expect($entry->fresh()->entityLabel())->toBe("Subtask #{$subtask->id} (deleted)");
});

test('a subtask whose parent is gone is named by its own title alone', function () {
    $subtask = Subtask::create(['task_id' => $this->task->id, 'title' => 'Write tests']);
    $entry = latestEntry('subtask.created');
    DB::table('tasks')->where('id', $this->task->id)->update(['organization_id' => Organization::create(['name' => 'Elsewhere', 'slug' => 'elsewhere', 'code' => 'ELS', 'accent_color' => '#000000'])->id]);

    // A parent the viewer's scope can't load (here: gone from the lookup)
    // must not break the label.
    $subtask->setRelation('task', null);
    $entry->primedRecord = $subtask;

    expect($entry->entityLabel())->toBe('Write tests');
});

// --- Notifications ----------------------------------------------------------

test('the email names the task by code and title, exactly once in the subject and once in the body', function () {
    $this->task->update(['status' => 'in_progress']);
    $entry = latestEntry('task.status_changed');

    $mail = (new AuditEventMailNotification($entry, 'Task status changed'))->toMail($this->staff);

    expect($mail->subject)->toBe('Task status changed: BGS-0001 Fix invoice PDF layout')
        ->and(substr_count(implode("\n", $mail->introLines), 'BGS-0001 Fix invoice PDF layout'))->toBe(1)
        ->and(substr_count(implode("\n", $mail->introLines), 'BGS-0001'))->toBe(1);
});

test('the in-app notification carries the code in its label and message', function () {
    $this->task->update(['status' => 'in_progress']);
    $data = (new AuditEventDatabaseNotification(latestEntry('task.status_changed'), 'Task status changed'))->toDatabase($this->staff);

    expect($data['entity_label'])->toBe('BGS-0001 Fix invoice PDF layout')
        ->and($data['message'])->toStartWith('Task status changed: BGS-0001 Fix invoice PDF layout — ')
        ->and(substr_count($data['message'], 'BGS-0001'))->toBe(1);
});

test('the @mention notification names the task by code, the title in quotes', function () {
    $data = (new MentionedInCommentNotification($this->task->fresh()))->toDatabase($this->staff);

    expect($data['message'])->toBe('You are mentioned in task BGS-0001 "Fix invoice PDF layout".');

    // Without a code (SQLite keeps the column nullable): the title alone.
    DB::table('tasks')->where('id', $this->task->id)->update(['task_number' => null]);
    expect((new MentionedInCommentNotification($this->task->fresh()))->toDatabase($this->staff)['message'])
        ->toBe('You are mentioned in task "Fix invoice PDF layout".');
});

// --- The Audit Trail page ---------------------------------------------------

test('task.created shows the task code, and task.code_changed shows the old and new codes', function () {
    $other = Organization::create(['name' => 'Solava', 'slug' => 'slv', 'code' => 'SLV', 'accent_color' => '#534AB7']);
    $otherDept = Department::create(['organization_id' => $other->id, 'name' => 'Ops', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $other->id, 'name' => 'Platform', 'description' => 'd']);
    $this->task->update(['organization_id' => $other->id, 'project_id' => $otherProject->id, 'department_id' => $otherDept->id]);

    expect(latestEntry('task.created')->describeChanges())->toStartWith('Task code: BGS-0001; ')
        ->and(latestEntry('task.code_changed')->describeChanges())->toBe('Code changed from BGS-0001 to SLV-0001')
        ->and(latestEntry('task.code_changed')->actionLabel())->toBe('Task Code Changed');

    $html = $this->actingAs($this->owner)->get('/audit-trail')->assertOk()->getContent();
    expect($html)->toContain('Task code: BGS-0001; ')
        ->toContain('Code changed from BGS-0001 to SLV-0001')
        ->toContain('Task Code Changed')
        // The task now goes by its new code everywhere, its old rows included.
        ->toContain('SLV-0001 Fix invoice PDF layout')
        // The action filter lists the new action.
        ->toContain('<option value="task.code_changed"');
});

test('the Audit Trail page costs the same number of queries for 5 entries as for 20', function () {
    $measure = function () {
        app()->forgetScopedInstances();

        return countQueries(fn () => $this->actingAs($this->owner)->get('/audit-trail')->assertOk());
    };

    // A mix: tasks created (with an assignee), reassigned, subtasks and a
    // comment, so every kind of label is on the page. The beforeEach task
    // is entry 1.
    $makeEntries = function (int $count) {
        for ($i = 0; $i < $count; $i++) {
            $task = messageTask($this, 'Task '.uniqid());
            match ($i % 3) {
                0 => Subtask::create(['task_id' => $task->id, 'title' => 'Sub '.$i, 'assignee_id' => $this->staff->id]),
                1 => $task->update(['assignee_id' => null]),
                2 => Comment::create(['task_id' => $task->id, 'user_id' => $this->owner->id, 'body' => '<p>Note '.$i.'</p>']),
            };
        }
    };

    // At least one of each kind in the small run too, so the large one
    // adds rows, not kinds.
    $makeEntries(3);
    expect(AuditLog::count())->toBeGreaterThanOrEqual(5);
    $measure();
    $small = $measure();

    $makeEntries(8);
    expect(AuditLog::count())->toBeGreaterThanOrEqual(20)->toBeLessThanOrEqual(25);
    $large = $measure();

    expect($large)->toBe($small);
});
