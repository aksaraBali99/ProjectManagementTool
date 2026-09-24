<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * The document/attachment button in the shared rich-text editor (Task
 * Description, Comments) uploads through here. Unlike image/audio/video,
 * this isn't purely a storage upload: attaching a document from the editor
 * mirrors Jira — it's not an inline preview/embed, it's a real Document
 * record (the same model/table the standalone Documents page and the Edit
 * Task page's existing "attach an existing document" picker both already
 * use), attached to the task via the existing task_documents pivot, with
 * a file-chip node in the text as a clickable reference to it. Two
 * consequences that set this controller apart from
 * RichTextImageController/RichTextAudioController/RichTextVideoController:
 *
 *   - store() creates the Document row and task_documents attachment
 *     immediately, in the same request as the file upload — there's a
 *     real task_id to attach to right away.
 *   - storePending() deliberately does NOT create a Document row at all
 *     (see its own docblock) — TaskManagementController::store() creates
 *     one, straight from the reconciled file-chip, once the task (and so
 *     a task_id to attach to) actually exists. This is the one meaningful
 *     way this controller diverges from the other three's otherwise
 *     identical shape.
 *
 * Permissions follow the same rule as image/audio/video, not
 * DocumentPolicy::create's stricter management-tier gate: whoever can
 * already edit the field (TaskPolicy::update for Description, the
 * ordinary comment-permission rule for Comments) can attach a document
 * through the editor, by this task's own explicit instruction — the same
 * relationship RichTextAudioController::store() already has to
 * TaskPolicy, just naturally extended to documents too. A staff member
 * who could never create a standalone Document via the Documents page
 * (DocumentPolicy::create requires management-tier + manage_documents)
 * can still attach one this way, exactly mirroring Jira's own model where
 * anyone who can comment can attach a file regardless of a separate
 * "manage attachments" permission.
 */
class RichTextDocumentController extends Controller
{
    /**
     * access_level defaults to Internal unconditionally, per the task's
     * own instruction — no prompt, since there's no clear reason a
     * mid-upload interruption to ask would be worth the friction here
     * (the document's access level can always be changed later from the
     * Documents page itself, same as any other document).
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'context' => ['required', Rule::in(['description', 'comment'])],
        ]);

        if ($data['context'] === 'description') {
            Gate::authorize('update', $task);
        } else {
            Gate::authorize('view', $task);
            Gate::authorize('create', Comment::class);
        }

        try {
            $stored = app(FileStorageService::class)->upload($request->file('file'), FileCategory::Document, $task->id);
        } catch (FileStorageException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $document = Document::create([
            'organization_id' => $task->organization_id,
            'uploaded_by' => auth()->id(),
            'name' => $request->file('file')->getClientOriginalName(),
            'link' => $stored->url,
            'access_level' => DocumentAccessLevel::Internal,
        ]);

        // Same underlying record as the Documents page/tab, not a
        // parallel thing that merely looks similar — this attachment is
        // what makes it show up there too.
        $task->documents()->attach($document->id);

        return response()->json(['url' => $stored->url, 'name' => $document->name], 201);
    }

    /**
     * For the Add Task page, where the task doesn't exist yet. Uploads the
     * file bytes to a pending path exactly like image/audio/video's own
     * storePending() — but, unlike them, deliberately creates NO Document
     * row and NO task_documents attachment here: there is no task_id yet
     * for either to point at. The file-chip the editor inserts after this
     * call carries only the pending URL and the original filename (see
     * buildDocumentUpload() in rich-text-editor.js); TaskManagementController
     * ::store() is what turns each file-chip still in the description into
     * a real Document + attachment, the moment a task_id finally exists.
     *
     * If the Add Task page is abandoned, the orphaned file is swept up by
     * media:cleanup-stale-pending exactly like any other pending upload —
     * there's no Document row to also worry about, since one was never
     * created.
     */
    public function storePending(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['nullable', 'integer'],
            'pending_id' => ['required', 'uuid'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        Gate::authorize('create', [Task::class, $project->organization_id, $data['department_id'] ?? null]);

        try {
            $stored = app(FileStorageService::class)->uploadPending($request->file('file'), FileCategory::Document, $data['pending_id']);
        } catch (FileStorageException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['url' => $stored->url, 'name' => $request->file('file')->getClientOriginalName()], 201);
    }
}
