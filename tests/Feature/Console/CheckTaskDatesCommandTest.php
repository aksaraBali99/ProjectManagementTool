<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;

/**
 * SOL-0022: solava:check-task-dates lists tasks and subtasks due before
 * their start date, across every company, and changes nothing.
 */
beforeEach(function () {
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
});

function checkDatesTask(Project $project, Department $dept, string $title, ?string $start, ?string $due): Task
{
    return Task::create([
        'organization_id' => $project->organization_id,
        'project_id' => $project->id,
        'department_id' => $dept->id,
        'title' => $title,
        'priority' => 'medium',
        'status' => 'pending',
        'start_date' => $start,
        'due_date' => $due,
    ]);
}

test('with nothing to report it says so', function () {
    checkDatesTask($this->project, $this->dept, 'Fine', '2026-12-01', '2026-12-05');

    $this->artisan('solava:check-task-dates')
        ->expectsOutput('No task or subtask breaks the due date rules.')
        ->assertSuccessful();
});

test('it lists tasks and subtasks due before their start date, and changes nothing', function () {
    $late = checkDatesTask($this->project, $this->dept, 'Started late', '2026-12-10', '2026-12-05');
    $fine = checkDatesTask($this->project, $this->dept, 'Fine', '2026-12-01', '2026-12-05');
    $fine->subtasks()->create(['title' => 'Backwards subtask', 'start_date' => '2026-12-04', 'due_date' => '2026-12-02']);
    $fine->subtasks()->create(['title' => 'Good subtask', 'start_date' => '2026-12-01', 'due_date' => '2026-12-02']);

    $this->artisan('solava:check-task-dates')
        ->expectsOutputToContain('Tasks with a due date earlier than the start date: 1')
        ->expectsOutputToContain('Started late')
        ->expectsOutputToContain('Subtasks with a due date earlier than the start date: 1')
        ->expectsOutputToContain('Backwards subtask')
        ->doesntExpectOutputToContain('Good subtask')
        ->assertSuccessful();

    expect($late->fresh()->due_date->toDateString())->toBe('2026-12-05');
});

test('it lists tasks due before one of their subtasks', function () {
    $task = checkDatesTask($this->project, $this->dept, 'Due before its subtask', '2026-12-01', '2026-12-05');
    $task->subtasks()->create(['title' => 'Runs late', 'start_date' => '2026-12-01', 'due_date' => '2026-12-15']);
    $fine = checkDatesTask($this->project, $this->dept, 'Due after its subtasks', '2026-12-01', '2026-12-20');
    $fine->subtasks()->create(['title' => 'On time', 'start_date' => '2026-12-01', 'due_date' => '2026-12-15']);

    $this->artisan('solava:check-task-dates')
        ->expectsOutputToContain('Tasks due before one of their subtasks: 1')
        ->expectsOutputToContain('Due before its subtask')
        ->doesntExpectOutputToContain('Due after its subtasks')
        ->assertSuccessful();
});

test('the same day stored two ways is not listed (SQLite keeps the text it was given)', function () {
    $task = checkDatesTask($this->project, $this->dept, 'Same day', '2026-11-27', '2026-11-27');
    $subtask = $task->subtasks()->create(['title' => 'Same day subtask']);
    DB::table('subtasks')->where('id', $subtask->id)->update(['start_date' => '2026-11-27 00:00:00', 'due_date' => '2026-11-27']);
    DB::table('tasks')->where('id', $task->id)->update(['start_date' => '2026-11-27 00:00:00', 'due_date' => '2026-11-27']);

    $this->artisan('solava:check-task-dates')
        ->expectsOutput('No task or subtask breaks the due date rules.')
        ->assertSuccessful();
});
