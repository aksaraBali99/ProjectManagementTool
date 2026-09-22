<?php

namespace App\Http\Controllers;

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Services\FileStorageService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * The audio button in the shared rich-text editor (Task Description,
 * Comments) uploads through here — task #4 phase 4, mirroring
 * RichTextImageController exactly (same permission model, same
 * FileStorageService pipeline, just FileCategory::Audio instead of
 * ::Image). Kept as its own controller rather than a shared one taking a
 * category parameter, matching how this codebase already keeps each
 * upload target's authorization logic in its own small, readable method
 * rather than behind a generic dispatch.
 */
class RichTextAudioController extends Controller
{
    /**
     * For an existing task: Description follows the same rule as editing
     * the task itself (TaskPolicy::update — assignee or management-tier),
     * Comment follows the same rule as posting a comment (can view the
     * task, plus the blanket "can comment at all" check) — inherited from
     * the field the audio is being attached to, not a new permission of
     * its own. See RichTextImageController::store() for the identical
     * image-upload version of this same rule.
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

        return $this->upload(fn (FileStorageService $service) => $service->upload($request->file('file'), FileCategory::Audio, $task->id));
    }

    /**
     * For the Add Task page, where the task doesn't exist yet — identical
     * to RichTextImageController::storePending(), just FileCategory::Audio.
     * $pendingId is the SAME pendingMediaId the page generated for the
     * image button too (see TaskManagementController::create()); one id
     * covers every category the Add Task page's editor uploads, since
     * FileStorageService::reconcilePendingFiles() moves everything a
     * description references in one generic pass, not per category.
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

        return $this->upload(fn (FileStorageService $service) => $service->uploadPending($request->file('file'), FileCategory::Audio, $data['pending_id']));
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
