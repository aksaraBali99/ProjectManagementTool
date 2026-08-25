<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('a failed Department row blocks Task rows referencing it', function () {
    $batch = runImportValidation([
        'Departments' => [['id' => 'DP-9999', 'name' => 'Marketing', 'company' => 'Org A']],
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'A task',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    $deptRow = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();
    $taskRow = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($deptRow->validation_status->value)->toBe('error');
    expect($taskRow->resolved_action->value)->toBe('blocked');
    expect($taskRow->validation_message)->toContain('Blocked');
    expect($taskRow->validation_message)->toContain('Department');
});

test('a failed Task row blocks its Subtask/Document/Comment rows by Task Ref', function () {
    $batch = runImportValidation([
        'Tasks' => [[
            // missing required Title -> this Task row fails its own validation
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
        'Subtasks' => [['task_ref' => '1', 'title' => 'A subtask']],
        'Task Documents' => [['task_ref' => '1', 'name' => 'Doc', 'link' => 'https://example.com']],
        'Task Comments' => [['task_ref' => '1', 'body' => 'A comment']],
    ], $this->owner);

    $taskRow = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();
    $subtaskRow = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();
    $documentRow = $batch->importRows()->where('sheet_name', 'Task Documents')->firstOrFail();
    $commentRow = $batch->importRows()->where('sheet_name', 'Task Comments')->firstOrFail();

    expect($taskRow->validation_status->value)->toBe('error');

    foreach ([$subtaskRow, $documentRow, $commentRow] as $row) {
        expect($row->resolved_action->value)->toBe('blocked');
        expect($row->validation_message)->toContain('Blocked');
    }
});

test('Subtask/Document/Comment rows with an unresolvable Task Ref fail validation', function () {
    $batch = runImportValidation([
        'Subtasks' => [['task_ref' => '99', 'title' => 'A subtask']],
        'Task Documents' => [['task_ref' => '99', 'name' => 'Doc', 'link' => 'https://example.com']],
        'Task Comments' => [['task_ref' => '99', 'body' => 'A comment']],
    ], $this->owner);

    foreach (['Subtasks', 'Task Documents', 'Task Comments'] as $sheetName) {
        $row = $batch->importRows()->where('sheet_name', $sheetName)->firstOrFail();
        expect($row->validation_status->value)->toBe('error');
        expect($row->validation_message)->toContain('does not match any row on the Tasks tab');
    }
});
