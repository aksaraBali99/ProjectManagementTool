<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Services\LinkPreviewResult;
use App\Services\LinkPreviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * The editor calls here the moment a user pastes a bare URL alone on its
 * own line (task #4, Smart Links) — same permission shape as
 * RichTextImageController/RichTextAudioController/RichTextVideoController/
 * RichTextDocumentController's two actions (an existing task's field vs.
 * the Add Task page, before a task_id exists), but no file is ever
 * uploaded here and nothing is stored under a task-scoped path — see
 * LinkPreviewService and the link_previews migration for why this cache
 * is global rather than task-scoped, which is also why, unlike the
 * document upload flow, there's no pending-path reconciliation step
 * needed on task save: the resolved title/image/domain already went into
 * the pasted node's own attributes at paste time, and the global cache
 * row already exists regardless of whether a task_id exists yet.
 *
 * Never a 422/500 on an unresolvable URL — a plain hyperlink, not a
 * broken chip, is the correct outcome for the editor to fall back to, so
 * `available: false` is itself a successful response, not an error.
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
            Gate::authorize('create', Comment::class);
        }

        return $this->respond(app(LinkPreviewService::class)->resolve($data['url'], $request->user()));
    }

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
            'image' => $result->image,
            'domain' => $result->domain,
        ]);
    }
}
