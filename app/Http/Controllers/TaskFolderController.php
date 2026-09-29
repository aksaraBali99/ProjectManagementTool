<?php

namespace App\Http\Controllers;

use App\Exceptions\FolderAlreadyLinkedException;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Task;
use App\Policies\DocumentPolicy;
use App\Services\TaskDocumentLinker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * task #73 phase 4: attaching a whole folder to a task — the natural
 * sibling of TaskDocumentController (Phase 3's single-file picker/attach/
 * detach), same panel structure, same gates reused verbatim
 * (TaskPolicy::attachDocuments()/unlinkDocuments() — no new policy
 * methods, since folder-attach needs exactly the same check file-attach
 * already has: manage_documents + can view the task + not a Client, and
 * that Client exclusion is already unconditional in attachDocuments(),
 * requiring no "addition" for folders).
 */
class TaskFolderController extends Controller
{
    /**
     * The folder picker's list — folders in the task's company, browsable
     * by anyone who clears the task-level gate (folders have no
     * visibility of their own, see DocumentFolder's own docblock),
     * excluding ones already linked to this task. No access-level
     * filtering at all, unlike the document picker — DocumentFolder::
     * scopeAttachableTo() is the whole query.
     */
    public function index(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('attachDocuments', $task);

        $data = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = DocumentFolder::query()->attachableTo($task)->orderBy('name');

        $search = trim((string) ($data['search'] ?? ''));
        if ($search !== '') {
            // Same ESCAPE-as-bound-parameter approach as the document
            // picker's search (TaskDocumentController::index()) — see its
            // own extensive comment for why the escape character can't be
            // inlined into the raw SQL text as a literal.
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $query->whereRaw('name LIKE ? ESCAPE ?', ["%{$escaped}%", '\\']);
        }

        $paginated = $query->paginate(25, ['*'], 'page', $data['page'] ?? 1);

        $paths = DocumentFolder::pathsFor($task->organization_id);

        $paginated->getCollection()->transform(fn (DocumentFolder $folder) => [
            'id' => $folder->id,
            'name' => $folder->name,
            'path' => $paths[$folder->id] ?? $folder->name,
        ]);

        return response()->json($paginated);
    }

    /**
     * 404, not 422, for a folder that doesn't exist or belongs to another
     * company — same "never reveal existence beyond what's already
     * implied" collapse TaskDocumentController::attach() uses, and folders
     * have no per-folder visibility to check beyond company membership
     * (see DocumentFolder's own docblock), so there's no equivalent of the
     * document endpoint's Gate::allows('view', ...) check here at all.
     */
    public function attach(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('attachDocuments', $task);

        $data = $request->validate([
            'folder_id' => ['required', 'integer'],
        ]);

        $folder = DocumentFolder::find($data['folder_id']);

        if ($folder === null || $folder->organization_id !== $task->organization_id) {
            abort(404);
        }

        $linker = app(TaskDocumentLinker::class);

        try {
            DB::transaction(function () use ($task, $folder, $linker) {
                // Locked and re-checked, not trusted from the read above —
                // same reasoning as the document attach endpoint's own
                // lockForUpdate(): a concurrent delete or company change
                // between the check and this point can't race a link past
                // it. No access-level/view() re-check needed here (unlike
                // the document version) since folders have none to begin
                // with.
                $locked = DocumentFolder::whereKey($folder->id)->lockForUpdate()->first();

                if ($locked === null || $locked->organization_id !== $task->organization_id) {
                    abort(404);
                }

                $linker->attachFolder($task, $locked);
            });
        } catch (FolderAlreadyLinkedException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['folder' => $folder]);
    }

    /**
     * Same rule as TaskDocumentController::detach(): manage_documents plus
     * being able to view the task, via TaskPolicy::unlinkDocuments() —
     * reused verbatim, no folder-specific gate. 404 for a folder that
     * doesn't belong to the task's own company, mirroring detach()'s own
     * 404 collapse (see its docblock for why this app aligns the two
     * endpoints' status codes rather than letting them disagree).
     */
    public function detach(Task $task, DocumentFolder $folder): JsonResponse
    {
        Gate::authorize('unlinkDocuments', $task);

        if ($folder->organization_id !== $task->organization_id) {
            abort(404);
        }

        app(TaskDocumentLinker::class)->detachFolder($task, $folder);

        return response()->json(['detached' => true]);
    }

    /**
     * A linked folder's DIRECT children (files and subfolders one level
     * down) for the task page's expand control — also reused, unchanged,
     * for expanding a SUBFOLDER one level further (a subfolder isn't
     * itself "linked" to the task, it's just browsed transitively under
     * an already-linked ancestor, so this deliberately does NOT check
     * task_folder_links at all — only that $folder belongs to the task's
     * own company, same defense as attach()'s explicit check above).
     *
     * Gated on being able to view the task AND NOT a Client-role user —
     * deliberately NOT manage_documents (viewing what's already linked is
     * available to any non-Client task viewer, same as directly-attached
     * files already are; manage_documents only gates the attach/detach
     * BUTTONS). This is the hard, query-level Client exclusion the task's
     * own spec calls for: a Client gets 404 here regardless of whether the
     * UI would ever show them the row (it doesn't), not a client-side hide.
     *
     * Subfolders returned unfiltered (folders have no visibility of their
     * own); documents filtered through DocumentPolicy::viewableIds() in
     * ONE batched pass, not a Gate::allows('view', ...) call per file —
     * genuinely computed for the CURRENT viewer, not cached from whoever
     * linked the folder.
     */
    public function expand(Task $task, DocumentFolder $folder): JsonResponse
    {
        $viewer = auth()->user();

        if ($folder->organization_id !== $task->organization_id
            || ! Gate::allows('view', $task)
            || $viewer->isClientInOrg($task->organization_id)) {
            abort(404);
        }

        $subfolders = DocumentFolder::where('parent_id', $folder->id)->orderBy('name')->get(['id', 'name']);

        $childDocuments = Document::where('folder_id', $folder->id)->with('uploader')->get();
        $viewableIds = app(DocumentPolicy::class)->viewableIds($viewer, $task->organization_id, $childDocuments);
        $visibleDocuments = $childDocuments->filter(fn (Document $document) => $viewableIds->contains($document->id))
            ->sortBy('name')
            ->values();

        return response()->json([
            'folders' => $subfolders->map(fn (DocumentFolder $subfolder) => [
                'id' => $subfolder->id,
                'name' => $subfolder->name,
            ])->values(),
            'documents' => $visibleDocuments->map(fn (Document $document) => [
                'id' => $document->id,
                'name' => $document->name,
                'access_level' => $document->access_level->value,
                'url' => $document->url,
            ])->values(),
        ]);
    }
}
