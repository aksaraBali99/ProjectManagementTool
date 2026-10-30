<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 6: the Kanban Completed column shows the most recently
 * completed first — completed_at DESC, then id DESC, no completed_at last
 * — on the board and through Load more. The other columns keep phase 5's
 * priority / due date / id order.
 *
 * Expected orders are built in PHP, independently of the SQL.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Board Org', 'slug' => 'board-org', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function makeKanbanOrderTask(object $context, string $status, string $priority = 'medium', ?string $dueDate = null): Task
{
    return Task::create([
        'organization_id' => $context->org->id,
        'project_id' => $context->project->id,
        'department_id' => $context->dept->id,
        'title' => 'Card',
        'priority' => $priority,
        'status' => $status,
        'due_date' => $dueDate,
    ]);
}

function expectedCompletedColumnOrder(int $organizationId): array
{
    return Task::where('organization_id', $organizationId)
        ->where('status', TaskStatus::Completed->value)
        ->get()
        ->sort(fn (Task $a, Task $b) => [$a->completed_at === null, -($a->completed_at?->timestamp ?? 0), -$a->id]
            <=> [$b->completed_at === null, -($b->completed_at?->timestamp ?? 0), -$b->id])
        ->pluck('id')
        ->all();
}

test('the Completed column is newest completion first, then id desc, nulls last, on the board and through Load more', function () {
    // 70 completed cards: repeated completion times (so the id tie-break
    // matters), a few with no completed_at, and priorities that would
    // order them differently if priority still applied.
    $times = ['2026-10-01 09:00:00', '2026-10-20 12:00:00', null, '2026-10-05 08:30:00', '2026-10-20 12:00:00', '2026-09-15 17:00:00'];
    $priorities = ['low', 'high', 'medium'];
    for ($i = 0; $i < 70; $i++) {
        $task = makeKanbanOrderTask($this, 'completed', $priorities[$i % 3]);
        DB::table('tasks')->where('id', $task->id)->update(['completed_at' => $times[$i % count($times)]]);
    }

    $expected = expectedCompletedColumnOrder($this->org->id);
    expect($expected)->toHaveCount(70);

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    $completed = $response->viewData('columns')->firstWhere('status', TaskStatus::Completed);

    expect($completed['tasks']->pluck('task.id')->all())->toBe(array_slice($expected, 0, 30));

    // Spot-check the shape of the order, not just the PHP mirror of it.
    $first = Task::find($expected[0]);
    $last = Task::find(end($expected));
    expect($first->completed_at->toDateTimeString())->toBe('2026-10-20 12:00:00')
        ->and($last->completed_at)->toBeNull();

    expect(kanbanColumnIdsAcrossBatches($this->management, $this->org->id, TaskStatus::Completed))->toBe($expected);
});

test('completing a card puts it at the top of the Completed column', function () {
    for ($i = 0; $i < 3; $i++) {
        $task = makeKanbanOrderTask($this, 'completed', 'high');
        DB::table('tasks')->where('id', $task->id)->update(['completed_at' => '2026-10-0'.($i + 1).' 10:00:00']);
    }
    $justDone = makeKanbanOrderTask($this, 'pending', 'low');

    $this->actingAs($this->management)->patchJson(route('tasks.update-status', $justDone), ['status' => 'completed'])->assertOk();

    $completed = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()
        ->viewData('columns')->firstWhere('status', TaskStatus::Completed);

    expect($completed['tasks']->first()['task']->id)->toBe($justDone->id);
});

test('the other columns keep the phase 5 order', function () {
    $dueDates = ['2026-11-20', null, '2026-11-05'];
    $priorities = ['low', 'high', 'medium', 'high'];
    for ($i = 0; $i < 12; $i++) {
        makeKanbanOrderTask($this, 'pending', $priorities[$i % 4], $dueDates[$i % 3]);
    }

    $priorityRank = array_flip(array_map(fn (Priority $case) => $case->value, Priority::cases()));
    $expected = Task::where('status', 'pending')->get()
        ->sort(fn (Task $a, Task $b) => [$priorityRank[$a->priority->value], $a->due_date === null, $a->due_date?->timestamp ?? 0, $a->id]
            <=> [$priorityRank[$b->priority->value], $b->due_date === null, $b->due_date?->timestamp ?? 0, $b->id])
        ->pluck('id')->all();

    $pending = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()
        ->viewData('columns')->firstWhere('status', TaskStatus::Pending);

    expect($pending['tasks']->pluck('task.id')->all())->toBe($expected);
});
