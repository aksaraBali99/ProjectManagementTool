<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskDocumentController extends Controller
{
    public function attach(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $data = $request->validate([
            'document_id' => ['required', 'integer', 'exists:documents,id'],
        ]);

        $document = Document::findOrFail($data['document_id']);
        Gate::authorize('view', $document);

        if ($document->organization_id !== $task->organization_id) {
            abort(422, 'Document must belong to the task\'s company.');
        }

        $task->documents()->syncWithoutDetaching([$document->id]);

        return response()->json(['document' => $document]);
    }

    /**
     * task #73 phase 2: gated by TaskPolicy::unlinkDocuments() — manage_
     * documents plus being able to view the task — not task-edit rights.
     * This is what the Documents page's own delete/edit dialogs reuse for
     * their Unlink buttons too (no separate route; see those controllers'
     * own docblocks), not just the Task edit page's inline Detach button.
     *
     * Also authorizes against the document itself and confirms it belongs
     * to the task's own company, mirroring attach() above — without
     * these, unlinkDocuments() alone (a task-scoped, manage_documents-in-
     * the-task's-org check) would let a manage_documents holder sever an
     * attachment to a document they have no visibility into at all, in an
     * org other than the task's own.
     */
    public function detach(Task $task, Document $document): JsonResponse
    {
        Gate::authorize('unlinkDocuments', $task);
        Gate::authorize('view', $document);

        if ($document->organization_id !== $task->organization_id) {
            abort(422, 'Document must belong to the task\'s company.');
        }

        $task->documents()->detach($document->id);

        return response()->json(['detached' => true]);
    }
}
