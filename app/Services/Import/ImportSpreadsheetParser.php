<?php

namespace App\Services\Import;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Reads an uploaded .xlsx into plain per-sheet arrays — no validation
 * logic here, purely extraction. Column->field mapping comes from
 * ImportSheetSchema (the same schema the template builder uses for
 * headers), so header text and parse mapping can never drift apart. The
 * hidden "Reference" sheet is never parsed — it's a dropdown source, not
 * a data tab.
 */
class ImportSpreadsheetParser
{
    private const FIRST_DATA_ROW = 3;

    /**
     * @return array<string, array<int, array{row_number: int, cells: array<string, string|null>}>>
     */
    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheets = [];

        foreach (ImportSheetSchema::sheetOrder() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $sheets[$sheetName] = $sheet ? $this->parseSheet($sheet, $sheetName) : [];
        }

        return $sheets;
    }

    /**
     * @return array<int, array{row_number: int, cells: array<string, string|null>}>
     */
    private function parseSheet(Worksheet $sheet, string $sheetName): array
    {
        $columns = ImportSheetSchema::columns($sheetName);
        $highestRow = $sheet->getHighestDataRow();
        $rows = [];

        for ($rowNumber = self::FIRST_DATA_ROW; $rowNumber <= $highestRow; $rowNumber++) {
            $cells = [];
            foreach ($columns as $column) {
                $cells[$column['field']] = $this->cellValue($sheet, "{$column['column']}{$rowNumber}");
            }

            $isBlankRow = collect($cells)->every(fn ($value) => $value === null);
            if ($isBlankRow) {
                continue;
            }

            $rows[] = ['row_number' => $rowNumber, 'cells' => $cells];
        }

        return $rows;
    }

    /**
     * Normalizes every cell to a trimmed string (or null when blank) —
     * including dates, which get formatted as d/m/Y regardless of whether
     * the source cell was a real Excel date or the user typed matching
     * text — so downstream validation always sees one consistent shape
     * and raw_data stays plain JSON-safe scalars for the review grid.
     */
    private function cellValue(Worksheet $sheet, string $coordinate): ?string
    {
        $cell = $sheet->getCell($coordinate);
        $value = $cell->getValue();

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && ExcelDate::isDateTime($cell)) {
            return ExcelDate::excelToDateTimeObject($value)->format('d/m/Y');
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
