<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentController extends Controller
{
    use ResolvesCurrentOrganization;

    public function index(?Organization $organization = null): View
    {
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->documentOrganizationIds())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('documents.index', [
                'organizations' => $organizations,
                'organization' => null,
                'documents' => collect(),
                'canManage' => false,
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $documents = Document::where('organization_id', $organization->id)
            ->with('uploader')
            ->get()
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->sortBy('name')
            ->values();

        return view('documents.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'documents' => $documents,
            'canManage' => Gate::allows('create', [Document::class, $organization->id]),
        ]);
    }

    public function create(?Organization $organization = null): View
    {
        $manageableOrgIds = auth()->user()->manageableOrganizationIds();
        abort_if(empty($manageableOrgIds), 403);

        $organizations = Organization::whereIn('id', $manageableOrgIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        abort_if($organizations->isEmpty(), 403);

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        Gate::authorize('create', [Document::class, $organization->id]);

        return view('documents.create', ['organization' => $organization]);
    }

    /**
     * Creates a document in a company's library. Three callers, same
     * endpoint: the standalone Add Document page (from_documents_page=1,
     * redirects to the Documents list), the Task list page's "+ Add new
     * document" button (plain form POST, no task_id, redirects back to
     * the Task list), and the Task Edit page's inline "add new document"
     * (AJAX, includes task_id to attach the new document to that task in
     * the same step).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:2048', 'url'],
            'access_level' => ['required', Rule::enum(DocumentAccessLevel::class)],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
        ], [], [
            'organization_id' => 'company',
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

        if ($request->boolean('from_documents_page')) {
            return redirect()->route('documents.index', $document->organization_id)->with('status', 'Document added.');
        }

        return back()->with('status', 'Document added.');
    }
}
