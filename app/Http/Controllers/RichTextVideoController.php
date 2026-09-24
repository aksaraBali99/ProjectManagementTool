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
 * The video button in the shared rich-text editor (Task Description,
 * Comments) uploads through here — task #4 phase 5, mirroring
 * RichTextAudioController exactly (same permission model, same
 * FileStorageService pipeline, just FileCategory::Video instead of
 * ::Audio). Kept as its own controller rather than a shared one taking a
 * category parameter, matching how this codebase already keeps each
 * upload target's authorization logic in its own small, readable method
 * rather than behind a generic dispatch.
 */
class RichTextVideoController extends Controller
{
    /**
     * For an existing task: Description follows the same rule as editing
     * the task itself (TaskPolicy::update — assignee or management-tier),
     * Comment follows the same rule as posting a comment (can view the
     * task, plus the blanket "can comment at all" check) — inherited from
     * the field the video is being attached to, not a new permission of
     * its own. See RichTextAudioController::store() for the identical
     * audio-upload version of this same rule.
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'context' => ['required', Rule::in(['description', 'comment'])],
            // task #4, clipboard paste — see RichTextImageController
            // ::store()'s identical fields for the full reasoning; a
            // pasted VIDEO FILE (copied via file explorer/Finder) reaches
            // this exactly the same way a pasted image does. Raw
            // screen-recording-tool clipboard bytes are a genuine,
            // inconsistent browser limitation this deliberately doesn't
            // try to force — see rich-text-editor.js.
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
            // See RichTextImageController::store()'s identical branch —
            // the naming and the actual upload happen INSIDE
            // PastedMediaNamer's own transaction+lock, so a rejected file
            // (oversized, disallowed type) never burns a number.
            return $this->upload(fn (FileStorageService $service) => app(PastedMediaNamer::class)->withNextFilename(
                $task,
                FileCategory::Video,
                $data['title'] ?? null,
                fn (string $filename) => $service->upload($request->file('file'), FileCategory::Video, $task->id, $filename),
            ));
        }

        return $this->upload(fn (FileStorageService $service) => $service->upload($request->file('file'), FileCategory::Video, $task->id));
    }

    /**
     * For the Add Task page, where the task doesn't exist yet — identical
     * to RichTextAudioController::storePending(), just FileCategory::Video.
     * $pendingId is the SAME pendingMediaId the page generated for the
     * image/audio buttons too (see TaskManagementController::create());
     * one id covers every category the Add Task page's editor uploads,
     * since FileStorageService::reconcilePendingFiles() moves everything a
     * description references in one generic pass, not per category.
     */
    public function storePending(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['nullable', 'integer'],
            'pending_id' => ['required', 'uuid'],
            // task #4, clipboard paste — see RichTextImageController
            // ::storePending()'s identical field for the full reasoning.
            'filename' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        Gate::authorize('create', [Task::class, $project->organization_id, $data['department_id'] ?? null]);

        return $this->upload(fn (FileStorageService $service) => $service->uploadPending($request->file('file'), FileCategory::Video, $data['pending_id'], $data['filename'] ?? null));
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
