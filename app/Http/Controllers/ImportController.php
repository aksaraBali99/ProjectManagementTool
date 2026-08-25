<?php

namespace App\Http\Controllers;

use App\Enums\ImportBatchStatus;
use App\Http\Requests\Import\UploadImportRequest;
use App\Models\ImportBatch;
use App\Services\Import\ImportCommitService;
use App\Services\Import\ImportSheetSchema;
use App\Services\Import\ImportTemplateBuilder;
use App\Services\Import\ImportValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('import.view');

        return view('import.index');
    }

    public function upload(UploadImportRequest $request, ImportValidator $validator): RedirectResponse
    {
        Gate::authorize('import.view');

        $file = $request->file('file');

        $rowCounts = $validator->countRows($file->getRealPath());
        $totalRows = array_sum($rowCounts);

        if ($totalRows > ImportValidator::MAX_TOTAL_ROWS) {
            return back()->withErrors([
                'file' => "This file has {$totalRows} rows across all tabs, over the ".ImportValidator::MAX_TOTAL_ROWS.'-row limit per import. Split it into smaller files and upload separately.',
            ]);
        }

        $batch = ImportBatch::create([
            'uploaded_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending_review',
        ]);

        $storedPath = $file->storeAs("imports/{$batch->id}", $file->getClientOriginalName(), 'local');
        $batch->update(['stored_path' => $storedPath]);

        $validator->validate($batch, Storage::disk('local')->path($storedPath));

        return redirect()->route('import.review', $batch);
    }

    public function review(ImportBatch $batch): View
    {
        Gate::authorize('import.view');

        $rowsBySheet = $batch->importRows()->orderBy('row_number')->get()->groupBy('sheet_name');

        // Re-key in the canonical tab order rather than groupBy()'s
        // arbitrary insertion order, so the review grid always reads
        // top-to-bottom exactly like the template's own tab order.
        $orderedRowsBySheet = collect(ImportSheetSchema::sheetOrder())
            ->mapWithKeys(fn (string $sheetName) => [$sheetName => $rowsBySheet->get($sheetName, collect())]);

        return view('import.review', [
            'batch' => $batch,
            'rowsBySheet' => $orderedRowsBySheet,
            'errorCount' => $batch->importRows()->where('validation_status', 'error')->count(),
            'warningCount' => $batch->importRows()->where('validation_status', 'warning')->count(),
        ]);
    }

    public function commit(Request $request, ImportBatch $batch, ImportCommitService $service): View
    {
        Gate::authorize('import.view');

        abort_unless($batch->status === ImportBatchStatus::PendingReview, 403, 'This batch is not pending review.');
        abort_if($batch->importRows()->where('validation_status', 'error')->exists(), 422, 'Cannot commit while errors remain.');

        $hasWarnings = $batch->importRows()->where('validation_status', 'warning')->exists();
        abort_if($hasWarnings && ! $request->boolean('acknowledge_warnings'), 422, 'Acknowledge the warnings before committing.');

        $summary = $service->commit($batch, auth()->user());

        return view('import.summary', ['summary' => $summary]);
    }

    public function downloadTemplate(ImportTemplateBuilder $builder): StreamedResponse
    {
        Gate::authorize('import.view');

        $spreadsheet = $builder->build();

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'Solava_Import_Template_'.now()->format('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
