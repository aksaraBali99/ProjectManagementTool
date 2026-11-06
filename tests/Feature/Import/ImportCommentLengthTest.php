<?php

use App\Models\Comment;
use App\Models\ImportRow;
use App\Services\Import\ImportCommitService;
use App\Services\Import\ImportValidationContext;
use App\Services\Import\ImportValidator;

/**
 * task #122: an imported comment has the same size cap as one posted in
 * the app (Comment::MAX_BODY_LENGTH), not the old 2,000 characters.
 */
beforeEach(function () {
    $this->owner = createImportOwner();
});

/** A workbook with one task, and the given comment on it. */
function workbookWithComment(string $body): array
{
    return [
        'Companies' => [['name' => 'New Co']],
        'Departments' => [['name' => 'Marketing', 'company' => 'New Co']],
        'Projects' => [['name' => 'Website Refresh', 'description' => 'd', 'company' => 'New Co']],
        'Tasks' => [[
            'task_ref' => '1', 'project_name' => 'Website Refresh', 'company' => 'New Co', 'department' => 'Marketing',
            'title' => 'Draft homepage copy', 'priority' => 'Medium', 'status' => 'Pending',
        ]],
        'Task Comments' => [['task_ref' => '1', 'body' => $body]],
    ];
}

test('a 5,000-character comment imports in full', function () {
    $body = str_repeat('Imported comment text. ', 218).'end';
    expect(mb_strlen($body))->toBeGreaterThanOrEqual(5000);

    $batch = runImportValidation(workbookWithComment($body), $this->owner);
    $row = ImportRow::where('import_batch_id', $batch->id)->where('sheet_name', 'Task Comments')->firstOrFail();
    expect($row->validation_status->value ?? $row->validation_status)->not->toBe('error');

    app(ImportCommitService::class)->commit($batch, $this->owner);

    expect(Comment::where('body', $body)->count())->toBe(1);
});

test('a comment over the cap is refused with the new message', function () {
    // Not through a spreadsheet: PhpSpreadsheet cuts every cell to Excel's
    // own 32,767-character limit, on write and on read, so a real import
    // can't reach the cap. The check is still the shared rule; this calls
    // the validator's comment-row step with one row that does.
    $validator = app(ImportValidator::class);
    $method = new ReflectionMethod($validator, 'validateTaskComments');
    $context = (new ReflectionClass(ImportValidationContext::class))->newInstanceWithoutConstructor();

    $result = $method->invoke($validator, [
        ['row_number' => 2, 'cells' => ['task_ref' => '1', 'body' => str_repeat('a', Comment::MAX_BODY_LENGTH + 1)]],
    ], $context);

    expect($result)->toHaveCount(1)
        ->and($result[0]['validation_status'])->toBe('error')
        ->and($result[0]['validation_message'])->toBe('Comment Body is too long (more than 200,000 characters).');
});
