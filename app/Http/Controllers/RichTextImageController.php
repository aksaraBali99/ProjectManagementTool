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
        ]);

        if ($data['context'] === 'description') {
            Gate::authorize('update', $task);
        } else {
            Gate::authorize('view', $task);
            Gate::authorize('create', Comment::class);
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
        ]);

        $project = Project::findOrFail($data['project_id']);

        Gate::authorize('create', [Task::class, $project->organization_id, $data['department_id'] ?? null]);

        return $this->upload(fn (FileStorageService $service) => $service->uploadPending($request->file('file'), FileCategory::Image, $data['pending_id']));
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
