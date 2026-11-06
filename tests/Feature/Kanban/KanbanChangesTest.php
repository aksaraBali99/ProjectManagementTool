<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\KanbanController;
use App\Models\AccessPermission;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AuditEventDatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * task #72 phase 10: the Kanban board's live updates. kanban.changes
 * returns what changed since the last poll, for this viewer:
 *
 * - cards: changed tasks that belong on their board now, rendered exactly
 *   as the board renders them;
 * - removed: the other changed ids (deactivated, no longer visible,
 *   completed outside the window), ids only;
 * - totals / older: the board's own header counts;
 * - cursor: server time, for the next poll.
 *
 * Plus the sort key the board places a card by, and the subtask touch
 * that makes a subtask assignee's board notice the parent task.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Live Co', 'slug' => 'live-co', 'accent_color' => '#1D9E75']);
    $this->otherOrg = Organization::create(['name' => 'Other Co', 'slug' => 'other-co', 'accent_color' => '#534AB7']);
    $this->deptA = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptB = Department::create(['organization_id' => $this->org->id, 'name' => 'Operations', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->projectB = Project::create(['organization_id' => $this->org->id, 'name' => 'Project B', 'description' => 'd']);

    $member = function (string $role, ?Organization $org = null): User {
        $user = User::factory()->create();
        OrgMember::create([
            'organization_id' => ($org ?? $this->org)->id,
            'user_id' => $user->id,
            'role_id' => Role::where('slug', $role)->firstOrFail()->id,
        ]);

        return $user;
    };

    $this->management = $member('management');
    $this->staffWithAccess = $member('staff');
    AccessPermission::create(['user_id' => $this->staffWithAccess->id, 'organization_id' => $this->org->id, 'department_id' => $this->deptA->id, 'allowed' => true]);
    $this->staffWithoutAccess = $member('staff');
    $this->staffAssignee = $member('staff');
    $this->staffSubtaskAssignee = $member('staff');
    $this->client = $member('client');
    $this->projectA->clients()->attach($this->client->id);
    $this->otherManagement = $member('management', $this->otherOrg);

    foreach ([$this->staffWithAccess, $this->staffAssignee] as $staff) {
        $this->projectA->staff()->attach($staff->id);
        $this->projectB->staff()->attach($staff->id);
    }

    $make = fn (array $attributes) => Task::create(array_merge([
        'organization_id' => $this->org->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task '.uniqid(),
        'priority' => 'medium',
        'status' => 'pending',
    ], $attributes));

    $this->tasks = collect([
        'a1' => $make(['title' => 'Dept A pending', 'assignee_id' => $this->staffWithAccess->id, 'due_date' => '2026-11-05']),
        'a2' => $make(['title' => 'Dept A in progress', 'status' => 'in_progress', 'priority' => 'high']),
        'b1' => $make(['title' => 'Dept B assigned', 'department_id' => $this->deptB->id, 'assignee_id' => $this->staffAssignee->id]),
        'b2' => $make(['title' => 'Dept B on Project B', 'department_id' => $this->deptB->id, 'project_id' => $this->projectB->id, 'priority' => 'low']),
        'b3' => $make(['title' => 'Dept B subtask only', 'department_id' => $this->deptB->id, 'project_id' => $this->projectB->id, 'status' => 'in_review']),
        'done' => $make(['title' => 'Completed recently', 'status' => 'completed']),
    ]);
    Subtask::create(['task_id' => $this->tasks['b3']->id, 'title' => 'Step', 'assignee_id' => $this->staffSubtaskAssignee->id]);

    // Completed before the 14-day window: on the board only with Show all.
    $this->oldDone = $make(['title' => 'Completed long ago', 'status' => 'completed']);
    DB::table('tasks')->where('id', $this->oldDone->id)->update(['completed_at' => '2026-09-01 09:00:00']);

    // Everything above is older than the polls below.
    $this->travel(1)->minutes();
});

function kanbanChanges(object $context, User $user, mixed $since, array $query = []): array
{
    return $context->actingAs($user)
        ->getJson(route('kanban.changes', [$context->org] + $query + ($since === null ? [] : ['since' => $since])))
        ->assertOk()
        ->json();
}

function kanbanBoardCursor(object $context, User $user): int
{
    return $context->actingAs($user)->get(route('kanban', $context->org))->assertOk()->viewData('cursor');
}

dataset('kanban change viewers', [
    'management' => ['management'],
    'staff with department access' => ['staffWithAccess'],
    'staff without department access' => ['staffWithoutAccess'],
    'staff who is only a task assignee' => ['staffAssignee'],
    'staff who is only a subtask assignee' => ['staffSubtaskAssignee'],
    'client' => ['client'],
]);

test('the board renders the server time as its first cursor', function () {
    expect(kanbanBoardCursor($this, $this->management))->toBe(now()->getTimestamp());
});

test('a changed task comes back as exactly the card the board renders; an unchanged one does not', function (string $viewer) {
    $user = $this->{$viewer};
    $cursor = kanbanBoardCursor($this, $user);
    $this->travel(10)->seconds();

    // Changed for everyone who can see it: management, the department's
    // staff (who may move it, as its assignee) and the client.
    $task = $this->tasks['a1'];
    $task->update(['title' => 'Renamed', 'priority' => 'high', 'due_date' => '2026-11-02']);
    $this->travel(5)->seconds();

    $json = kanbanChanges($this, $user, $cursor);

    expect($json['cursor'])->toBe(now()->getTimestamp())
        ->and(collect($json['cards'])->pluck('id')->all())->toBe([$task->id])
        ->and($json['removed'])->toBe([]);

    $card = $json['cards'][0];
    expect($card['status'])->toBe('pending')
        ->and($card['sort_key'])->toBe($task->fresh()->kanbanSortKey());

    $board = $this->actingAs($user)->get(route('kanban', $this->org))->assertOk()->getContent();
    expect(kanbanCardMarkup($card['html'], $task->id))->toBe(kanbanCardMarkup($board, $task->id))
        ->and($card['html'])->toContain('Renamed');
})->with([
    'management' => ['management'],
    'staff who may move it' => ['staffWithAccess'],
    'client' => ['client'],
]);

test('a save stamped up to 10 seconds before the cursor (committed late, or in the same second) is still returned', function () {
    $since = now()->getTimestamp();

    $this->tasks['a2']->update(['title' => 'Same second']);

    // Stamped before the previous poll's cursor but, say, committed after
    // it: the next poll still reads it.
    $this->travel(-10)->seconds();
    $this->tasks['b1']->update(['title' => 'Ten seconds before']);

    $this->travel(-1)->seconds();
    $this->tasks['b2']->update(['title' => 'Eleven seconds before']);

    $this->travel(11)->seconds();
    $ids = collect(kanbanChanges($this, $this->management, $since)['cards'])->pluck('id');

    expect(KanbanController::CHANGES_OVERLAP_SECONDS)->toBe(10)
        ->and($ids)->toContain($this->tasks['a2']->id)
        ->toContain($this->tasks['b1']->id)
        ->not->toContain($this->tasks['b2']->id);
});

test('an invalid or missing since reads as now: no cards, fresh totals and cursor', function (mixed $since) {
    $this->tasks['a1']->update(['title' => 'Changed just now']);

    $json = kanbanChanges($this, $this->management, $since);

    expect($json['cards'])->toBe([])
        ->and($json['removed'])->toBe([])
        ->and($json['cursor'])->toBe(now()->getTimestamp())
        ->and($json['totals'])->toHaveKeys(['pending', 'in_progress', 'in_review', 'completed']);
})->with([
    'missing' => [null],
    'not a number' => ['yesterday'],
    'negative' => ['-5'],
    'decimal' => ['1.5'],
]);

test('a row stamped in the future is not returned on every poll', function () {
    $since = now()->getTimestamp();
    DB::table('tasks')->where('id', $this->tasks['a1']->id)->update(['updated_at' => now()->addDay()]);

    expect(kanbanChanges($this, $this->management, $since)['cards'])->toBe([]);
});

test('a deactivated task comes back in removed', function () {
    $since = now()->getTimestamp();
    $this->tasks['a2']->delete();

    $json = kanbanChanges($this, $this->management, $since);

    expect($json['removed'])->toBe([$this->tasks['a2']->id])->and($json['cards'])->toBe([]);
});

test('a task moved to a department the staff member cannot see is removed for them, and a card for management', function () {
    $since = now()->getTimestamp();
    $task = $this->tasks['a2'];
    $task->update(['department_id' => $this->deptB->id]);

    expect(kanbanChanges($this, $this->staffWithAccess, $since)['removed'])->toBe([$task->id])
        ->and(collect(kanbanChanges($this, $this->management, $since)['cards'])->pluck('id')->all())->toBe([$task->id]);
});

test('a task reassigned away from a staff member who only saw it as assignee is removed for them', function () {
    $since = now()->getTimestamp();
    $task = $this->tasks['b1'];
    $task->update(['assignee_id' => null]);

    $json = kanbanChanges($this, $this->staffAssignee, $since);

    expect($json['removed'])->toBe([$task->id])->and($json['cards'])->toBe([]);
});

test('a completed task outside the window is removed by default, and a card with completed=all', function () {
    $since = now()->getTimestamp();
    $task = $this->tasks['a2'];
    $task->update(['status' => 'completed']);
    DB::table('tasks')->where('id', $task->id)->update(['completed_at' => '2026-09-01 09:00:00']);

    expect(kanbanChanges($this, $this->management, $since)['removed'])->toBe([$task->id]);

    $all = kanbanChanges($this, $this->management, $since, ['completed' => 'all']);
    expect(collect($all['cards'])->pluck('id')->all())->toBe([$task->id])
        ->and($all['cards'][0]['status'])->toBe('completed')
        ->and($all['removed'])->toBe([]);
});

test('cards are exactly what visibleTo() and the window allow, and removed carries ids only', function (string $viewer) {
    $user = $this->{$viewer};
    $since = now()->getTimestamp();

    // Every task in the company changes, inactive ones and other
    // companies' included.
    $this->tasks['a2']->delete();
    Task::withoutGlobalScopes()->update(['updated_at' => now()]);

    $json = kanbanChanges($this, $user, $since);
    $window = $this->org->completedWindowStart();

    $expected = Task::visibleTo($user, $this->org->id)->openOrCompletedWithin($window)->pluck('id')->sort()->values()->all();
    $changed = Task::withTrashed()->where('organization_id', $this->org->id)->pluck('id');
    $cards = collect($json['cards'])->pluck('id')->sort()->values()->all();

    expect($cards)->toBe($expected)
        ->and(collect($json['removed'])->sort()->values()->all())->toBe($changed->diff($expected)->sort()->values()->all());

    foreach ($json['removed'] as $id) {
        expect($id)->toBeInt();
    }

    // Nothing about a task this viewer can't see reaches the response.
    $raw = json_encode($json);
    Task::withTrashed()->whereNotIn('id', $expected)->pluck('title')->each(
        fn (string $title) => expect($raw)->not->toContain($title),
    );
})->with('kanban change viewers');

test('the viewers really see different slices of the board', function () {
    $count = fn (User $user) => Task::visibleTo($user, $this->org->id)->count();

    expect($count($this->management))->toBe(7)
        ->and($count($this->staffWithAccess))->toBe(4)
        ->and($count($this->staffWithoutAccess))->toBe(0)
        ->and($count($this->staffAssignee))->toBe(1)
        ->and($count($this->staffSubtaskAssignee))->toBe(1)
        ->and($count($this->client))->toBe(5);
});

test('totals match the board headers and the "older" count after changes, in both modes', function (array $query) {
    $since = now()->getTimestamp();
    $this->tasks['a1']->update(['status' => 'in_review']);
    $this->tasks['b2']->update(['status' => 'completed']);
    $this->tasks['a2']->delete();
    $this->travel(5)->seconds();

    $json = kanbanChanges($this, $this->management, $since, $query);
    $columns = $this->actingAs($this->management)->get(route('kanban', [$this->org] + $query))->assertOk()->viewData('columns');

    foreach ($columns as $column) {
        expect($json['totals'][$column['status']->value])->toBe($column['total'], $column['status']->value);
    }
    $completed = $columns->firstWhere('status', TaskStatus::Completed);
    expect($json['older'])->toBe($completed['older']);

    if ($query === []) {
        expect($json['older'])->toBe(1)->and($json['totals']['completed'])->toBe(2);
    } else {
        expect($json['older'])->toBe(0)->and($json['totals']['completed'])->toBe(3);
    }
})->with([
    'recent (default)' => [[]],
    'show all' => [['completed' => 'all']],
]);

test('more than 100 changed tasks is "stale": the board asks for a reload', function () {
    $since = now()->getTimestamp();
    $ids = Task::withTrashed()->where('organization_id', $this->org->id)->pluck('id');

    for ($i = $ids->count(); $i < 100; $i++) {
        Task::create([
            'organization_id' => $this->org->id, 'project_id' => $this->projectA->id, 'department_id' => $this->deptA->id,
            'title' => 'Bulk '.$i, 'priority' => 'low', 'status' => 'pending',
        ]);
    }
    Task::withoutGlobalScopes()->where('organization_id', $this->org->id)->update(['updated_at' => now()]);

    $json = kanbanChanges($this, $this->management, $since);
    expect($json)->not->toHaveKey('stale')->and($json['cards'])->toHaveCount(99);

    Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->projectA->id, 'department_id' => $this->deptA->id,
        'title' => 'One too many', 'priority' => 'low', 'status' => 'pending',
    ]);

    expect(kanbanChanges($this, $this->management, $since))->toBe(['cursor' => now()->getTimestamp(), 'stale' => true]);
});

test('the changes endpoint refuses who the board refuses', function () {
    $url = route('kanban.changes', [$this->org, 'since' => now()->getTimestamp()]);

    // Another company's management.
    $this->actingAs($this->otherManagement)->getJson($url)->assertForbidden();

    // view_kanban revoked from the staff role.
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $staffRole->permissions()->sync($staffRole->permissions()->where('slug', '!=', 'view_kanban')->pluck('permissions.id')->all());
    $this->actingAs($this->staffWithAccess)->getJson($url)->assertForbidden();

    // A deactivated company.
    $this->org->update(['is_active' => false]);
    $this->actingAs($this->management)->getJson($url)->assertForbidden();
    $this->org->update(['is_active' => true]);

    // An unknown company.
    $this->actingAs($this->management)->getJson('/kanban/999999/changes')->assertNotFound();

    // Guests: 401 for the board's JSON request, the login page otherwise.
    auth()->logout();
    $this->getJson($url)->assertUnauthorized();
    $this->get($url)->assertRedirect(route('login'));
});

test('sorting cards by data-sort-key gives exactly the board order, in every column', function () {
    $project = $this->projectA;
    $make = fn (string $status, string $priority, ?string $due, ?string $completedAt = null) => tap(Task::create([
        'organization_id' => $this->org->id, 'project_id' => $project->id, 'department_id' => $this->deptA->id,
        'title' => 'Sorted', 'priority' => $priority, 'status' => $status, 'due_date' => $due,
    ]), function (Task $task) use ($completedAt, $status) {
        if ($status === 'completed') {
            DB::table('tasks')->where('id', $task->id)->update(['completed_at' => $completedAt]);
        }
    });

    foreach (['pending', 'in_progress', 'in_review'] as $status) {
        foreach (['low', 'high', 'medium'] as $priority) {
            // Undated, two on the same day (the id decides), a later day,
            // an earlier day, and another undated one.
            $make($status, $priority, null);
            $make($status, $priority, '2026-11-20');
            $make($status, $priority, '2026-11-20');
            $make($status, $priority, '2026-12-01');
            $make($status, $priority, '2026-11-03');
            $make($status, $priority, null);
        }
    }
    // Completed: same completed_at (the id decides, newest first), none,
    // older and newer, all inside the window.
    foreach (['2026-10-29 10:00:00', null, '2026-10-29 10:00:00', '2026-10-20 08:00:00', '2026-10-29 18:30:00', null] as $completedAt) {
        $make('completed', 'medium', null, $completedAt);
    }

    $visible = Task::visibleTo($this->management, $this->org->id);
    foreach (TaskStatus::cases() as $status) {
        $query = (clone $visible)->where('status', $status->value);
        if ($status === TaskStatus::Completed) {
            $query->orderByDesc('tasks.completed_at')->orderByDesc('tasks.id');
        } else {
            $query->orderByEnum('priority', Priority::class)->orderByRaw('tasks.due_date IS NULL')->orderBy('tasks.due_date')->orderBy('tasks.id');
        }
        $tasks = $query->get();
        $byKey = $tasks->sortBy(fn (Task $task) => $task->kanbanSortKey(), SORT_STRING)->pluck('id')->all();

        expect($byKey)->toBe($tasks->pluck('id')->all(), $status->value)
            ->and($tasks->map->kanbanSortKey()->unique())->toHaveCount($tasks->count());
    }

    // And the board's own first batch is in that order too.
    $board = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();
    preg_match_all('/data-task-id="(\d+)" data-sort-key="([^"]+)"/', $board, $matches);
    expect($matches[2])->toHaveCount(count($matches[1]))->not->toBeEmpty();
});

test('every card carries its sort key, on the board and from Load more', function () {
    $board = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();

    foreach (kanbanCardIds($board) as $id) {
        expect($board)->toContain('data-task-id="'.$id.'" data-sort-key="'.Task::find($id)->kanbanSortKey().'"');
    }

    $json = $this->actingAs($this->management)->getJson(route('kanban.column', [$this->org, 'pending']))->assertOk()->json();
    expect($json['html'])->toContain('data-sort-key="'.$this->tasks['a1']->kanbanSortKey().'"');
});

describe('subtask assignee changes touch the parent task', function () {
    beforeEach(function () {
        $this->actingAs($this->management);
        $this->task = $this->tasks['b2'];
        $this->since = now()->getTimestamp();
        $this->travel(10)->seconds();
    });

    test('creating a subtask with an assignee touches the task, and their board gets the card', function () {
        Subtask::create(['task_id' => $this->task->id, 'title' => 'New step', 'assignee_id' => $this->staffAssignee->id]);

        expect($this->task->fresh()->updated_at->getTimestamp())->toBe(now()->getTimestamp())
            ->and(collect(kanbanChanges($this, $this->staffAssignee, $this->since)['cards'])->pluck('id'))->toContain($this->task->id);
    });

    test('creating one without an assignee, or ticking one off, does not', function () {
        $before = $this->task->fresh()->updated_at;

        $subtask = Subtask::create(['task_id' => $this->task->id, 'title' => 'Unassigned step']);
        $subtask->update(['is_done' => true, 'title' => 'Renamed step']);

        expect($this->task->fresh()->updated_at->equalTo($before))->toBeTrue();
    });

    test('reassigning a subtask touches the task: the old assignee loses it, the new one gets it', function () {
        $subtask = $this->tasks['b3']->subtasks()->firstOrFail();
        $since = now()->getTimestamp();
        $this->travel(5)->seconds();

        $subtask->update(['assignee_id' => $this->staffAssignee->id]);

        expect(kanbanChanges($this, $this->staffSubtaskAssignee, $since)['removed'])->toBe([$this->tasks['b3']->id])
            ->and(collect(kanbanChanges($this, $this->staffAssignee, $since)['cards'])->pluck('id')->all())->toContain($this->tasks['b3']->id);
    });

    test('deleting an assigned subtask touches the task, and it leaves that assignee\'s board', function () {
        $subtask = $this->tasks['b3']->subtasks()->firstOrFail();
        $since = now()->getTimestamp();
        $this->travel(5)->seconds();

        $subtask->delete();

        expect($this->tasks['b3']->fresh()->updated_at->getTimestamp())->toBe(now()->getTimestamp())
            ->and(kanbanChanges($this, $this->staffSubtaskAssignee, $since)['removed'])->toBe([$this->tasks['b3']->id]);
    });

    test('the touch writes no audit row and sends no notification of its own', function () {
        // Rules that notify everyone involved of every task event a Task
        // save could produce: assignments (which go to the new assignee
        // only), status and priority changes.
        $people = [$this->management, $this->staffAssignee, $this->staffWithAccess];
        foreach (['task_assigned', 'task_status_changed', 'task_priority_changed'] as $eventType) {
            NotificationSetting::create([
                'owner_id' => $this->owner->id,
                'event_type' => $eventType,
                'channel' => 'in_app',
                'recipients' => ['type' => 'users', 'ids' => array_map(fn (User $user) => $user->id, $people)],
                'is_active' => true,
            ]);
        }
        Notification::fake();
        $auditRows = AuditLog::count();
        $taskAuditRows = AuditLog::where('entity_type', 'task')->count();

        $subtask = Subtask::create(['task_id' => $this->task->id, 'title' => 'Step', 'assignee_id' => $this->staffAssignee->id]);
        $this->travel(5)->seconds();
        $subtask->update(['assignee_id' => $this->staffWithAccess->id]);
        $this->travel(5)->seconds();
        $subtask->delete();

        // The task was touched each time...
        expect($this->task->fresh()->updated_at->getTimestamp())->toBe(now()->getTimestamp());

        // ...yet the only audit rows are the three subtask ones.
        expect(AuditLog::count() - $auditRows)->toBe(3)
            ->and(AuditLog::where('entity_type', 'task')->count())->toBe($taskAuditRows);

        // Only the subtask's own assignment rows notify, each its new
        // assignee once; the touches add nothing for anyone.
        Notification::assertSentToTimes($this->staffAssignee, AuditEventDatabaseNotification::class, 1);
        Notification::assertSentToTimes($this->staffWithAccess, AuditEventDatabaseNotification::class, 1);
        Notification::assertNotSentTo($this->management, AuditEventDatabaseNotification::class);
        foreach ($people as $user) {
            Notification::assertNotSentTo($user, AuditEventDatabaseNotification::class,
                fn (AuditEventDatabaseNotification $notification) => $notification->auditLog->entity_type !== 'subtask');
        }
    });
});
