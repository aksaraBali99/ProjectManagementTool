<?php

namespace App\Http\Controllers;

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Services\FileStorageService;
use App\Services\PastedMediaNamer;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * The image button in the shared rich-text editor (Task Description,
 * Comments) uploads through here — one endpoint per storage target, since
 * the two targets are authorized by two different rules (see each method).
 *
 * Neither action duplicates FileStorageService's own size/type checking;
 * `file` is only validated here as "an uploaded file exists", and the
 * category-specific limits (config/filestorage.php) are enforced once, by
 * the service, converting its FileStorageException into a 422 either way.
 */
class RichTextImageController extends Controller
{
    /**
     * For an existing task: Description follows the same rule as editing
     * the task itself (TaskPolicy::update — assignee or management-tier),
     * Comment follows the same rule as posting a comment (can view the
     * task, plus the blanket "can comment at all" check) — inherited from
     * the field the image is being attached to, not a new permission of
     * its own.
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'context' => ['required', Rule::in(['description', 'comment'])],
            // task #4, clipboard paste: `pasted` flags that this file came
            // from a clipboard paste rather than the picker, which is the
            // ONLY thing that ever triggers the auto-filename below — a
            // picker upload never sends this, so it's never affected.
            // `title` is the Title field's value AT THE MOMENT OF PASTE
            // (read client-side, not this task's own possibly-stale saved
            // title — see rich-text-editor.js), which is also why it's
            // trusted here only as free text to slugify, never as a raw
            // filename itself.
            'pasted' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['context'] === 'description') {
            Gate::authorize('update', $task);
        } else {
            Gate::authorize('view', $task);
            Gate::authorize('create', Comment::class);
        }

        if ($data['pasted'] ?? false) {
            // The naming and the actual upload happen INSIDE
            // PastedMediaNamer's own transaction+lock (see its own
            // docblock) — a rejected file (oversized, disallowed type)
            // never burns a number, since the pasted_media row is only
            // written after $service->upload() itself succeeds.
            return $this->upload(fn (FileStorageService $service) => app(PastedMediaNamer::class)->withNextFilename(
                $task,
                FileCategory::Image,
                $data['title'] ?? null,
                fn (string $filename) => $service->upload($request->file('file'), FileCategory::Image, $task->id, $filename),
            ));
        }

        return $this->upload(fn (FileStorageService $service) => $service->upload($request->file('file'), FileCategory::Image, $task->id));
    }

    /**
     * For the Add Task page, where the task doesn't exist yet: authorized
     * the same way creating the task itself would be (TaskPolicy::create
     * against the currently-selected project's company — the same call
     * TaskManagementController::create()/store() already make), rather
     * than inventing a separate "can attach an image while drafting"
     * capability. $pendingId is a random id the page generated on load
     * (see TaskManagementController@create), not a database row.
     */
    public function storePending(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['nullable', 'integer'],
            'pending_id' => ['required', 'uuid'],
            // task #4, clipboard paste — the Add Task page has no task_id
            // yet to atomically count pasted_media against, and no
            // concurrent-paste risk for one person drafting one task, so
            // (unlike store() above) the client computes the whole
            // filename itself (slugifyTaskTitle() in rich-text-editor.js)
            // and just hands it over already-built. The strict allowlist
            // is this endpoint's only defense against that client-built
            // string being used as a storage path segment — a picker
            // upload never sends this field at all.
            'filename' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        Gate::authorize('create', [Task::class, $project->organization_id, $data['department_id'] ?? null]);

        return $this->upload(fn (FileStorageService $service) => $service->uploadPending($request->file('file'), FileCategory::Image, $data['pending_id'], $data['filename'] ?? null));
    }

    private function upload(Closure $attempt): JsonResponse
    {
        try {
            $stored = $attempt(app(FileStorageService::class));
        } catch (FileStorageException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['url' => $stored->url], 201);
    }
}
