<?php

use App\Models\ImportBatch;
use App\Models\User;
use App\Services\Import\ImportSheetSchema;
use App\Services\Import\ImportValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Builds a minimal .xlsx test fixture for the Import feature — one row
 * per array entry, per sheet, mapped through ImportSheetSchema so column
 * letters never need to be hardcoded in individual tests.
 *
 * @param  array<string, array<int, array<string, mixed>>>  $sheetsData  sheet name => rows, each row keyed by field name
 */
function buildImportTestFile(array $sheetsData): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $spreadsheet->removeSheetByIndex(0);

    foreach (ImportSheetSchema::sheetOrder() as $sheetName) {
        $sheet = new Worksheet($spreadsheet, $sheetName);
        $spreadsheet->addSheet($sheet);

        $columns = ImportSheetSchema::columns($sheetName);
        $rows = $sheetsData[$sheetName] ?? [];

        $rowNumber = 3;
        foreach ($rows as $rowData) {
            foreach ($columns as $column) {
                $value = $rowData[$column['field']] ?? null;
                if ($value !== null) {
                    $sheet->setCellValue("{$column['column']}{$rowNumber}", $value);
                }
            }
            $rowNumber++;
        }
    }

    $path = tempnam(sys_get_temp_dir(), 'solava-import-test').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    return new UploadedFile($path, 'test-import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
}

/**
 * Builds a fixture via buildImportTestFile(), creates an ImportBatch owned
 * by $uploader, and runs the real ImportValidator against it — the direct
 * (non-HTTP) path most validation-rule tests should use, so they can
 * assert on import_rows without going through the upload endpoint each
 * time.
 *
 * @param  array<string, array<int, array<string, mixed>>>  $sheetsData
 */
function runImportValidation(array $sheetsData, User $uploader): ImportBatch
{
    $file = buildImportTestFile($sheetsData);

    $batch = ImportBatch::create([
        'uploaded_by' => $uploader->id,
        'file_name' => 'test-import.xlsx',
        'status' => 'pending_review',
    ]);

    app(ImportValidator::class)->validate($batch, $file->getRealPath());

    return $batch->fresh();
}
