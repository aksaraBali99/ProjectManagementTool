<?php

namespace App\Http\Controllers;

use App\Services\Import\ImportTemplateBuilder;
use Illuminate\Support\Facades\Gate;
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
