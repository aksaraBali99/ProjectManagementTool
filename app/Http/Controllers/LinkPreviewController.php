<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Services\LinkPreviewResult;
use App\Services\LinkPreviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * The editor calls here the moment a bare URL is inserted as new content
 * — a paste alone on its own line, or the toolbar's "insert hyperlink"
 * button with nothing selected (task #4, Smart Links; the two client
 * paths were unified to both call this same endpoint through
 * buildLinkPreview()'s shared resolveAndInsert(), rather than the
 * toolbar button skipping resolution the way it originally did). Same
 * permission shape as RichTextImageController/RichTextAudioController/
 * RichTextVideoController/RichTextDocumentController's two actions (an
 * existing task's field vs. the Add Task page, before a task_id exists),
 * but no file is ever uploaded here and nothing is stored under a
 * task-scoped path — see LinkPreviewService and the link_previews
 * migration for why this cache is global rather than task-scoped.
 *
 * Never a 422/500 on an unresolvable URL — a plain hyperlink, not a
 * broken chip, is the correct outcome for the editor to fall back to, so
 * `available: false` is itself a successful response, not an error. The
 * Document is still tracked either way (see attachAsDocument() below) —
 * an uncaptioned link is a Document too, just named after its raw URL.
 */
class LinkPreviewController extends Controller
{
    public function resolve(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'context' => ['required', Rule::in(['description', 'comment'])],
        ]);

        if ($data['context'] === 'description') {
            Gate::authorize('update', $task);
        } else {
            Gate::authorize('view', $task);
            Gate::authorize('create', [Comment::class, $task]);
        }

        $service = app(LinkPreviewService::class);
        $result = $service->resolve($data['url'], $request->user());

        // Every link added to an EXISTING task is tracked immediately —
        // unlike the Add Task page (resolvePending() below), there's a
        // real task_id to attach to right now, the same reasoning
        // RichTextDocumentController::store() creates its Document
        // immediately rather than deferring.
        //
        // ...but only for someone who may actually add documents. This
        // call creates a real Document row, exactly like the editor's
        // file-upload button, so it needs the same manage_documents gate
        // (DocumentPolicy::create) that
        // RichTextDocumentController::store() applies — without it, a
        // role with "Add & edit documents" unticked could still mint
        // Documents just by pasting a URL into a comment.
        //
        // Gated around the attach, NOT the whole endpoint, deliberately:
        // the preview chip itself is just rich text, and the Add Task
        // page's equivalent (resolvePending()) creates no Document at all
        // and so needs no gate. Refusing the request outright here would
        // mean a staff member got preview chips while drafting a new task
        // but not when commenting on a saved one — an arbitrary split.
        // This way the rule is uniform: previews always resolve, Document
        // rows appear only for users who may create them.
        if (Gate::allows('create', [Document::class, $task->organization_id])) {
            $service->attachAsDocument($task, $data['url'], $result?->title);
        }

        return $this->respond($result);
    }

    /**
     * For the Add Task page, where the task doesn't exist yet. Resolves
     * the title exactly the same way, but deliberately does NOT create a
     * Document here — there's no task_id yet for task_documents to point
     * at. TaskManagementController::store()'s reconciliation step is what
     * turns each link-preview chip still in the description into a
     * tracked Document, once a real task_id finally exists — the same
     * deferral RichTextDocumentController::storePending() already uses
     * for uploaded documents.
     */
    public function resolvePending(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['nullable', 'integer'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        Gate::authorize('create', [Task::class, $project->organization_id, $data['department_id'] ?? null]);

        return $this->respond(app(LinkPreviewService::class)->resolve($data['url'], $request->user()));
    }

    private function respond(?LinkPreviewResult $result): JsonResponse
    {
        if ($result === null) {
            return response()->json(['available' => false]);
        }

        return response()->json([
            'available' => true,
            'url' => $result->url,
            'title' => $result->title,
            'domain' => $result->domain,
        ]);
    }
}
