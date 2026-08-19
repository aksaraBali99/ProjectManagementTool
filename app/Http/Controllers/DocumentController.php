<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{
    /**
     * Creates a document in a company's library. Two callers, same
     * endpoint: the Task list page's "+ Add new document" button (plain
     * form POST, no task_id, redirects back) and the Task Edit page's
     * inline "add new document" (AJAX, includes task_id to attach the
     * new document to that task in the same step).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:2048', 'url'],
            'access_level' => ['required', 'in:private,internal,public'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
        ]);

        Gate::authorize('create', [Document::class, $data['organization_id']]);

        $document = Document::create([
            'organization_id' => $data['organization_id'],
            'uploaded_by' => auth()->id(),
            'name' => $data['name'],
            'link' => $data['link'],
            'access_level' => $data['access_level'],
        ]);

        if (! empty($data['task_id'])) {
            $task = Task::findOrFail($data['task_id']);
            Gate::authorize('update', $task);

            if ($document->organization_id !== $task->organization_id) {
                abort(422, 'Document must belong to the task\'s company.');
            }

            $task->documents()->syncWithoutDetaching([$document->id]);
        }

        if ($request->expectsJson()) {
            return response()->json(['document' => $document], 201);
        }

        return back()->with('status', 'Document added.');
    }
}
