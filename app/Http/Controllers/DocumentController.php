<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Task;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $manageableOrgIds = auth()->user()->documentManageableOrganizationIds();
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

    /**
     * Every place a document/file-chip link is followed (the Documents
     * page, a task's Documents tab, a file-chip clicked inside a saved
     * description/comment) routes through here instead of linking
     * straight at `$document->link`, so a document this app itself
     * uploaded downloads under its real original name instead of the
     * bare tasks/{id}/documents/{uuid}.ext storage key a direct link
     * would save as.
     *
     * Takes the link itself, not a {document} route-bound id: a file-chip
     * embedded in already-saved rich text only ever has the raw URL (see
     * file-chip-extension.js) — there's no document_id anywhere in that
     * HTML to bind against — so resolving by the exact `link` value is
     * what one endpoint can serve both that and the two blade views that
     * do have a real Document on hand equally. The exact-match lookup
     * doubles as the only access check this needs against being handed an
     * arbitrary attacker-supplied URL: nothing resolves unless some
     * existing Document row's `link` already equals it verbatim.
     */
    public function download(Request $request): StreamedResponse|RedirectResponse
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);

        $document = Document::where('link', $data['url'])->first();
        abort_unless($document !== null, 404);

        // The stricter DocumentPolicy::view isn't the only door in here on
        // purpose — RichTextDocumentController's own docblock already
        // establishes that a document attached via a file-chip is meant
        // to be visible to anyone who can view the task it's attached to,
        // not gated behind the separate view_documents/management-tier
        // rule DocumentPolicy::view enforces for the standalone Documents
        // page. Either door being open is enough.
        $authorized = Gate::allows('view', $document)
            || $document->tasks()->get()->contains(fn (Task $task) => Gate::allows('view', $task));
        abort_unless($authorized, 403);

        return app(FileStorageService::class)->download($document->link, $document->name)
            ?? redirect()->away($document->link);
    }
}
