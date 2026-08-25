<?php

namespace App\Services\Import;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Role;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Builds the live Import template as a PhpSpreadsheet Spreadsheet — pulls
 * current DB state at generation time rather than shipping a static file.
 * Structure (tab order, header/legend rows, frozen panes, dropdown source
 * ranges) mirrors docs/Reference/Solva_Import_Template.xlsx, which was
 * inspected directly (unzipped, raw XML read) to confirm the exact layout;
 * this class replaces that file's placeholder content with real data.
 *
 * Companies/Departments are fully listed (small, bounded counts — a
 * handful of companies, a few departments each) so their hidden ID column
 * can drive rename/update detection on upload. Users/Company Roles/
 * Projects/Tasks/Subtasks/Task Documents/Task Comments instead get exactly
 * one illustrative example row each (unbounded in a mature company —
 * dumping every existing task/user into the sheet would make it enormous
 * and isn't needed for a "fill in new/changed rows" tool). Those example
 * rows are mutually consistent (same Task Ref chain, same example company)
 * and, where a real company/department already exists, reference it
 * directly so the template is importable as-is as a smoke test.
 */
class ImportTemplateBuilder
{
    // Excel's DataValidation is one object per cell (no true "range"
    // primitive in PhpSpreadsheet's model — the writer collapses adjacent
    // identical ones into a shared range in the XML output, but building
    // them is still one allocation per cell). 500 rows across every
    // dropdown column in every sheet was enough to blow PHP CLI's default
    // 128M memory_limit when several templates get built in one test run;
    // 200 comfortably covers a single import batch (the upload path itself
    // caps a batch at 5,000 total rows across all sheets) while a user who
    // truly needs more rows can just drag-fill the validation further in
    // Excel.
    private const DATA_VALIDATION_LAST_ROW = 200;

    // Solava's brand green (the same accent used for primary buttons and
    // the active sidebar border throughout the app) and its pale wash —
    // ties the template's look back to the rest of the app instead of a
    // generic black-on-white spreadsheet.
    private const HEADER_FILL_COLOR = '1D9E75';

    private const HEADER_FONT_COLOR = 'FFFFFF';

    private const LEGEND_FONT_COLOR = '0F6E56';

    private const LEGEND_FILL_COLOR = 'E1F5EE';

    private const HINT_FONT_COLOR = '9CA3AF';

    // The legend row is merged wider than the sheet's own data columns
    // (Companies only has 2, for example) purely so the instructional text
    // gets more horizontal room to wrap into — fewer wrapped lines means a
    // shorter row height, and the reader isn't stuck reading a tall,
    // narrow column of text squeezed into 2-11 columns.
    private const LEGEND_MERGE_COLUMN_COUNT = 30;

    // Beyond just relying on Excel's column-width-based auto-wrap, the
    // legend text is manually word-wrapped to this many characters per
    // line — keeps every line a predictable, easy-to-read length
    // regardless of the merged range's actual pixel width, and lets the
    // row height be computed exactly from the resulting line count instead
    // of guessed.
    private const LEGEND_LINE_LENGTH = 250;

    private const LEGEND_LINE_HEIGHT = 13;

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;

        $organizations = Organization::orderBy('id')->get(['id', 'name']);
        $departments = Department::with('organization:id,name')->orderBy('id')->get(['id', 'name', 'organization_id']);

        $exampleOrganizationName = $organizations->first()->name ?? 'Example Company';
        $exampleDepartmentName = $departments
            ->firstWhere('organization_id', $organizations->first()->id ?? null)
            ->name ?? 'Example Department';

        $this->buildCompaniesSheet($spreadsheet, $organizations);
        $this->buildDepartmentsSheet($spreadsheet, $departments);
        $this->buildReferenceSheet($spreadsheet);
        $this->buildUsersSheet($spreadsheet);
        $this->buildCompanyRolesSheet($spreadsheet, $exampleOrganizationName);
        $this->buildProjectsSheet($spreadsheet, $exampleOrganizationName);
        $this->buildTasksSheet($spreadsheet, $exampleOrganizationName, $exampleDepartmentName);
        $this->buildSubtasksSheet($spreadsheet);
        $this->buildTaskDocumentsSheet($spreadsheet);
        $this->buildTaskCommentsSheet($spreadsheet);

        // PhpSpreadsheet's constructor seeds one default "Worksheet" sheet
        // at index 0 — every real sheet above was appended after it, so
        // removing index 0 now leaves the remaining 10 in the exact order
        // they were added, with no gap to renumber.
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildCompaniesSheet(Spreadsheet $spreadsheet, Collection $organizations): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Companies');
        $lastColumn = $this->applyHeaderRow($sheet, 'Companies');
        $this->applyLegendRow($sheet, 'Companies', $lastColumn);
        $this->freezePane($sheet);

        $row = 3;
        foreach ($organizations as $organization) {
            $sheet->setCellValue("A{$row}", ImportIdCodec::encode(ImportIdCodec::COMPANY_PREFIX, $organization->id));
            $sheet->setCellValue("B{$row}", $organization->name);
            $row++;
        }

        $this->addHintRow($sheet, $row, 'B', '< add a new company here, leave column A blank >');
        $this->setColumnWidths($sheet, ['A' => 16, 'B' => 32]);
    }

    private function buildDepartmentsSheet(Spreadsheet $spreadsheet, Collection $departments): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Departments');
        $lastColumn = $this->applyHeaderRow($sheet, 'Departments');
        $this->applyLegendRow($sheet, 'Departments', $lastColumn);
        $this->freezePane($sheet);

        $row = 3;
        foreach ($departments as $department) {
            $sheet->setCellValue("A{$row}", ImportIdCodec::encode(ImportIdCodec::DEPARTMENT_PREFIX, $department->id));
            $sheet->setCellValue("B{$row}", $department->name);
            $sheet->setCellValue("C{$row}", $department->organization->name);
            $row++;
        }

        $this->addHintRow($sheet, $row, 'B', '< add a new department here, leave column A blank >');
        $this->addListValidation($sheet, 'C', 4, self::DATA_VALIDATION_LAST_ROW, 'Companies!$B$3:$B$500');
        $this->setColumnWidths($sheet, ['A' => 16, 'B' => 24, 'C' => 24]);
    }

    private function buildReferenceSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $this->addSheet($spreadsheet, ImportSheetSchema::referenceSheetName());

        $sheet->setCellValue('A1', 'Priorities');
        $sheet->setCellValue('B1', 'Task Statuses');
        $sheet->setCellValue('C1', 'Project Statuses');
        $sheet->setCellValue('D1', 'Roles');
        $sheet->setCellValue('E1', 'User Types');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $priorities = array_map(fn (Priority $p) => $p->label(), Priority::cases());
        $taskStatuses = array_map(fn (TaskStatus $s) => $s->label(), TaskStatus::cases());
        $projectStatuses = array_map(fn (ProjectStatus $s) => $s->label(), ProjectStatus::cases());
        $roles = Role::assignableInCompany()->orderBy('name')->pluck('name')->all();
        $userTypes = ['Employee', 'Client'];

        $this->writeColumn($sheet, 'A', 2, $priorities);
        $this->writeColumn($sheet, 'B', 2, $taskStatuses);
        $this->writeColumn($sheet, 'C', 2, $projectStatuses);
        $this->writeColumn($sheet, 'D', 2, $roles);
        $this->writeColumn($sheet, 'E', 2, $userTypes);

        $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
    }

    private function buildUsersSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Users');
        $lastColumn = $this->applyHeaderRow($sheet, 'Users');
        $this->applyLegendRow($sheet, 'Users', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray(['jdoe', 'Jane Doe', 'Employee', '', 'jane.doe@example.com', '+6281234567890', ''], null, 'A3');
        $sheet->fromArray(['client.acme', 'Acme Corp Contact', 'Client', '', 'contact@acmecorp.com', '+6281111111111', ''], null, 'A4');

        $this->addListValidation($sheet, 'C', 5, self::DATA_VALIDATION_LAST_ROW, 'Reference!$E$2:$E$3');
        $this->setColumnWidths($sheet, ['A' => 16, 'B' => 22, 'C' => 12, 'D' => 20, 'E' => 26, 'F' => 20, 'G' => 20]);
    }

    private function buildCompanyRolesSheet(Spreadsheet $spreadsheet, string $exampleOrganizationName): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Company Roles');
        $lastColumn = $this->applyHeaderRow($sheet, 'Company Roles');
        $this->applyLegendRow($sheet, 'Company Roles', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray(['jdoe', $exampleOrganizationName, 'Staff'], null, 'A3');
        $sheet->fromArray(['client.acme', $exampleOrganizationName, 'Client'], null, 'A4');

        $this->addListValidation($sheet, 'B', 5, self::DATA_VALIDATION_LAST_ROW, 'Companies!$B$3:$B$500');
        $this->addListValidation($sheet, 'C', 5, self::DATA_VALIDATION_LAST_ROW, 'Reference!$D$2:$D$4');
        $this->setColumnWidths($sheet, ['A' => 16, 'B' => 24, 'C' => 14]);
    }

    private function buildProjectsSheet(Spreadsheet $spreadsheet, string $exampleOrganizationName): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Projects');
        $lastColumn = $this->applyHeaderRow($sheet, 'Projects');
        $this->applyLegendRow($sheet, 'Projects', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray([
            '', 'Example Project', 'Short description of the project.', $exampleOrganizationName, '',
            ProjectStatus::Open->label(), Priority::Medium->label(), 'jdoe',
        ], null, 'A3');

        $this->addListValidation($sheet, 'D', 4, self::DATA_VALIDATION_LAST_ROW, 'Companies!$B$3:$B$500');
        $this->addListValidation($sheet, 'F', 4, self::DATA_VALIDATION_LAST_ROW, 'Reference!$C$2:$C$3');
        $this->addListValidation($sheet, 'G', 4, self::DATA_VALIDATION_LAST_ROW, 'Reference!$A$2:$A$4');
        $this->setColumnWidths($sheet, ['A' => 16, 'B' => 24, 'C' => 34, 'D' => 22, 'E' => 20, 'F' => 12, 'G' => 12, 'H' => 26]);
    }

    private function buildTasksSheet(Spreadsheet $spreadsheet, string $exampleOrganizationName, string $exampleDepartmentName): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Tasks');
        $lastColumn = $this->applyHeaderRow($sheet, 'Tasks');
        $this->applyLegendRow($sheet, 'Tasks', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray([
            1, 'Example Project', $exampleOrganizationName, $exampleDepartmentName, 'Example Task',
            'Short description of the task.', '', '', Priority::Medium->label(), TaskStatus::Pending->label(), 'jdoe',
        ], null, 'A3');

        $this->addListValidation($sheet, 'C', 4, self::DATA_VALIDATION_LAST_ROW, 'Companies!$B$3:$B$500');
        $this->addListValidation($sheet, 'D', 4, self::DATA_VALIDATION_LAST_ROW, 'Departments!$B$3:$B$500');
        $this->addListValidation($sheet, 'I', 4, self::DATA_VALIDATION_LAST_ROW, 'Reference!$A$2:$A$4');
        $this->addListValidation($sheet, 'J', 4, self::DATA_VALIDATION_LAST_ROW, 'Reference!$B$2:$B$5');
        $this->setColumnWidths($sheet, ['A' => 12, 'B' => 20, 'C' => 20, 'D' => 18, 'E' => 26, 'F' => 30, 'G' => 16, 'H' => 16, 'I' => 10, 'J' => 12, 'K' => 20]);
    }

    private function buildSubtasksSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Subtasks');
        $lastColumn = $this->applyHeaderRow($sheet, 'Subtasks');
        $this->applyLegendRow($sheet, 'Subtasks', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray([1, 'Example Subtask', '', '', ''], null, 'A3');

        $this->setColumnWidths($sheet, ['A' => 12, 'B' => 26, 'C' => 20, 'D' => 20, 'E' => 26]);
    }

    private function buildTaskDocumentsSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Task Documents');
        $lastColumn = $this->applyHeaderRow($sheet, 'Task Documents');
        $this->applyLegendRow($sheet, 'Task Documents', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray([1, 'Example Document', 'https://example.com/document'], null, 'A3');

        $this->setColumnWidths($sheet, ['A' => 12, 'B' => 28, 'C' => 34]);
    }

    private function buildTaskCommentsSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $this->addSheet($spreadsheet, 'Task Comments');
        $lastColumn = $this->applyHeaderRow($sheet, 'Task Comments');
        $this->applyLegendRow($sheet, 'Task Comments', $lastColumn);
        $this->freezePane($sheet);

        $sheet->fromArray([1, 'Example comment.'], null, 'A3');

        $this->setColumnWidths($sheet, ['A' => 12, 'B' => 50]);
    }

    // -- shared helpers --------------------------------------------------

    private function addSheet(Spreadsheet $spreadsheet, string $title): Worksheet
    {
        $sheet = new Worksheet($spreadsheet, $title);
        $spreadsheet->addSheet($sheet);

        return $sheet;
    }

    /** Writes the bold header row (row 1) from ImportSheetSchema and returns the last column letter used. */
    private function applyHeaderRow(Worksheet $sheet, string $sheetName): string
    {
        $columns = ImportSheetSchema::columns($sheetName);
        $lastColumn = 'A';

        foreach ($columns as $column) {
            $sheet->setCellValue("{$column['column']}1", $column['header']);
            $lastColumn = $column['column'];
        }

        $style = $sheet->getStyle("A1:{$lastColumn}1");
        $style->getFont()->setBold(true)->setSize(10)->getColor()->setRGB(self::HEADER_FONT_COLOR);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::HEADER_FILL_COLOR);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(36);

        return $lastColumn;
    }

    /**
     * Merges row 2 across a wide fixed range and writes the sheet's
     * instructional legend text into it, manually word-wrapped to
     * LEGEND_LINE_LENGTH characters per line so the row height can be
     * computed exactly from the resulting line count.
     */
    private function applyLegendRow(Worksheet $sheet, string $sheetName, string $lastColumn): void
    {
        $mergeColumnCount = max(self::LEGEND_MERGE_COLUMN_COUNT, Coordinate::columnIndexFromString($lastColumn));
        $mergeLastColumn = Coordinate::stringFromColumnIndex($mergeColumnCount);

        $sheet->mergeCells("A2:{$mergeLastColumn}2");

        $wrapped = wordwrap(ImportSheetSchema::legend($sheetName), self::LEGEND_LINE_LENGTH, "\n");
        $sheet->setCellValue('A2', $wrapped);

        $style = $sheet->getStyle('A2');
        $style->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
        $style->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB(self::LEGEND_FONT_COLOR);
        $style->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::LEGEND_FILL_COLOR);

        $lineCount = substr_count($wrapped, "\n") + 1;
        $sheet->getRowDimension(2)->setRowHeight($lineCount * self::LEGEND_LINE_HEIGHT + 10);
    }

    private function freezePane(Worksheet $sheet): void
    {
        $sheet->freezePane('A3');
    }

    private function addListValidation(Worksheet $sheet, string $column, int $fromRow, int $toRow, string $sourceRange): void
    {
        for ($row = $fromRow; $row <= $toRow; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid entry');
            $validation->setError('Please select a value from the dropdown list.');
            $validation->setFormula1($sourceRange);
        }
    }

    /** @param array<int, string|null> $values */
    private function writeColumn(Worksheet $sheet, string $column, int $fromRow, array $values): void
    {
        $row = $fromRow;
        foreach ($values as $value) {
            $sheet->setCellValue("{$column}{$row}", $value);
            $row++;
        }
    }

    private function addHintRow(Worksheet $sheet, int $row, string $column, string $text): void
    {
        $sheet->setCellValue("{$column}{$row}", $text);
        $sheet->getStyle("{$column}{$row}")->getFont()->setItalic(true)->getColor()->setRGB(self::HINT_FONT_COLOR);
    }

    /** @param array<string, int> $widths column letter => width */
    private function setColumnWidths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
}
