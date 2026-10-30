<?php

use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Carbon;

/**
 * task #72 phase 7: the window is a display rule on four pages. It never
 * blocks direct access (the task page, the Tasks list preview, links from
 * notifications), and it only ever REMOVES tasks: for management, staff
 * and clients, no page shows a task Task::visibleTo() wouldn't return.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);

    $this->old = makeWindowTask($this, 'completed', '2026-01-01 00:00:00', ['assignee_id' => $this->staff->id]);
});

test('an old completed task still opens on its task page and in the preview', function (string $who) {
    $this->actingAs($this->{$who})->get(route('tasks.edit', $this->old))->assertOk()
        ->assertSee($this->old->title)
        ->assertSee('Completed on')
        ->assertSee('Jan 1, 2026');

    $this->actingAs($this->{$who})->get(route('tasks.preview', $this->old))->assertOk();
})->with(['owner', 'management', 'staff', 'client']);

test('the window never adds a task the user could not already see', function (string $who) {
    // A department the staff member was never granted, and a project the
    // client isn't on — each with recent, old and open tasks.
    $hiddenDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Finance', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Other project', 'description' => 'd']);
    foreach ([['department_id' => $hiddenDept->id], ['project_id' => $otherProject->id]] as $where) {
        makeWindowTask($this, 'completed', '2026-10-28 08:00:00', $where);
        makeWindowTask($this, 'completed', '2026-03-01 08:00:00', $where);
        makeWindowTask($this, 'pending', null, $where);
    }
    makeWindowTask($this, 'completed', '2026-10-28 08:00:00');
    makeWindowTask($this, 'pending');

    $user = $this->{$who};
    $visible = Task::visibleTo($user, $this->org->id)->pluck('id')->all();
    expect($visible)->not->toBeEmpty();

    foreach ([[], ['completed' => 'all']] as $query) {
        // Kanban, every column and batch.
        foreach (TaskStatus::cases() as $status) {
            $board = $this->actingAs($user)->get(route('kanban', [$this->org] + $query));
            if ($board->status() === 403) {
                break;
            }
            $column = $board->viewData('columns')->firstWhere('status', $status);
            expect($column['tasks']->pluck('task.id')->diff($visible)->all())->toBe([]);
        }

        // Tasks list, every page.
        expect(array_diff(taskListIdsAcrossPages($user, $this->org->id, $query), $visible))->toBe([]);

        // Projects drilldowns.
        $projects = $this->actingAs($user)->get(route('projects.index', [$this->org] + $query))->assertOk()->viewData('projects');
        expect($projects->flatMap(fn ($project) => $project->tasks->pluck('id'))->diff($visible)->all())->toBe([]);
    }

    // Dashboard (no completed=all there; it links to the Tasks list).
    $dashboard = $this->actingAs($user)->get(route('dashboard', $this->org));
    if ($dashboard->status() === 200) {
        $shown = $dashboard->viewData('priorityGroups')->flatten(1)->pluck('id')
            ->merge($dashboard->viewData('myTasks')->pluck('id'));
        expect($shown->diff($visible)->all())->toBe([]);
    }
})->with(['management', 'staff', 'client']);
