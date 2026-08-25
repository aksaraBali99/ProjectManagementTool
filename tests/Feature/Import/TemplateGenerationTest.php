<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->staff = User::factory()->create();
});

/** Downloads the template via the real route and loads it back with PhpSpreadsheet, for structural assertions. */
function downloadTemplateSpreadsheet(User $actor): Spreadsheet
{
    $response = test()->actingAs($actor)->get('/import/template');
    $response->assertOk();

    $path = tempnam(sys_get_temp_dir(), 'solava-import-template').'.xlsx';
    file_put_contents($path, $response->streamedContent());

    $spreadsheet = IOFactory::load($path);
    unlink($path);

    return $spreadsheet;
}

test('a non-admin cannot download the import template', function () {
    $this->actingAs($this->staff)->get('/import/template')->assertForbidden();
});

test('a non-admin cannot view the import page', function () {
    $this->actingAs($this->staff)->get('/import')->assertForbidden();
});

test('an owner can download the import template as a valid xlsx with 10 sheets in the documented order', function () {
    $spreadsheet = downloadTemplateSpreadsheet($this->owner);

    expect($spreadsheet->getSheetCount())->toBe(10);

    $expectedOrder = [
        'Companies', 'Departments', 'Reference', 'Users', 'Company Roles',
        'Projects', 'Tasks', 'Subtasks', 'Task Documents', 'Task Comments',
    ];

    expect($spreadsheet->getSheetNames())->toBe($expectedOrder);

    $reference = $spreadsheet->getSheetByName('Reference');
    expect($reference->getSheetState())->toBe(Worksheet::SHEETSTATE_HIDDEN);
});

test('the template freezes panes at row 3 and marks required headers with an asterisk', function () {
    $spreadsheet = downloadTemplateSpreadsheet($this->owner);

    $companies = $spreadsheet->getSheetByName('Companies');
    expect($companies->getFreezePane())->toBe('A3');
    expect($companies->getCell('A1')->getValue())->toBe('ID (do not edit)');
    expect($companies->getCell('B1')->getValue())->toBe('Company Name*');

    $tasks = $spreadsheet->getSheetByName('Tasks');
    expect($tasks->getFreezePane())->toBe('A3');
    expect($tasks->getCell('E1')->getValue())->toBe('Title* (cannot be changed once imported)');
});

test('the Reference sheet task-status column uses live enum labels, not stale placeholder wording', function () {
    $spreadsheet = downloadTemplateSpreadsheet($this->owner);
    $reference = $spreadsheet->getSheetByName('Reference');

    expect($reference->getCell('B2')->getValue())->toBe('Pending');
    expect($reference->getCell('B3')->getValue())->toBe('Active');
    expect($reference->getCell('B4')->getValue())->toBe('Need Review');
    expect($reference->getCell('B5')->getValue())->toBe('Completed');
});

test('Companies and Departments tabs list existing live data with encoded IDs', function () {
    $organization = Organization::create(['name' => 'Live Test Co', 'slug' => 'live-test-co', 'accent_color' => '#1D9E75']);
    Department::create(['organization_id' => $organization->id, 'name' => 'Live Test Dept', 'color' => '#000000']);

    $spreadsheet = downloadTemplateSpreadsheet($this->owner);

    $companies = $spreadsheet->getSheetByName('Companies');
    expect($companies->getCell('A3')->getValue())->toBe('CO-'.str_pad((string) $organization->id, 4, '0', STR_PAD_LEFT));
    expect($companies->getCell('B3')->getValue())->toBe('Live Test Co');

    $departments = $spreadsheet->getSheetByName('Departments');
    expect($departments->getCell('B3')->getValue())->toBe('Live Test Dept');
    expect($departments->getCell('C3')->getValue())->toBe('Live Test Co');
});
