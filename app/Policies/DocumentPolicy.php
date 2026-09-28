<?php

namespace App\Policies;

use App\Enums\DocumentAccessLevel;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DocumentPolicy
{
    /**
     * Two independent paths in:
     *   - super_admin/owner/management: unconditional (private isn't
     *     secret from admins, just from other staff/clients).
     *   - anyone else holding view_documents in this company: gated
     *     further by access_level — private narrows to the uploader only,
     *     internal/public are both visible to any view_documents holder.
     *   - a client (who never holds view_documents — their visibility is
     *     handled here, not via that permission) sees a Public document
     *     only if it's linked, via task_documents, to a task on a project
     *     they're the client of. Internal/private are never visible to a
     *     client through this path.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($document->organization_id)) {
            return true;
        }

        if ($user->hasPermission('view_documents', $document->organization_id)) {
            return match ($document->access_level) {
                DocumentAccessLevel::Private => $document->uploaded_by === $user->id,
                DocumentAccessLevel::Internal, DocumentAccessLevel::Public => true,
            };
        }

        return $document->access_level === DocumentAccessLevel::Public
            && $document->tasks()->whereHas('project.clients', fn ($query) => $query->where('users.id', $user->id))->exists();
    }

    /**
     * task #73 phase 2: batched view() for the Documents page, which
     * needs "which of these documents can this viewer see" for a whole
     * page of rows, not one at a time — a per-row Gate::allows('view', ...)
     * call is a real N+1 (isManagementInOrg()/hasPermission() aren't
     * memoized, see their own docblocks), caught by the page's own
     * query-count regression test. Mirrors view()'s three branches
     * exactly, just computed once for the whole $documents collection:
     * the first two branches need no per-document query at all (a single
     * org-level check decides every row), and the client branch resolves
     * its per-document task/project/client linkage via one query instead
     * of one per row.
     *
     * @param  Collection<int, Document>  $documents  all already known to belong to $organizationId
     * @return Collection<int, int> the subset of $documents this user can view, by id
     */
    public function viewableIds(User $user, int $organizationId, Collection $documents): Collection
    {
        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)) {
            return $documents->pluck('id');
        }

        if ($user->hasPermission('view_documents', $organizationId)) {
            return $documents->filter(fn (Document $document) => match ($document->access_level) {
                DocumentAccessLevel::Private => $document->uploaded_by === $user->id,
                DocumentAccessLevel::Internal, DocumentAccessLevel::Public => true,
            })->pluck('id');
        }

        // whereNull('tasks.deleted_at'): a raw query builder join has no
        // idea Task uses SoftDeletes — Eloquent's global scope is what
        // makes view()'s own $document->tasks()->whereHas(...) above
        // silently exclude a soft-deleted (deactivated) task automatically.
        // Without this, a document linked only to a deactivated task would
        // count as client-visible here while view() denies it per-document
        // — exactly the disagreement the parity test exists to catch, just
        // for a state (a deactivated task) it didn't exercise.
        $clientVisibleDocumentIds = DB::table('task_documents')
            ->join('tasks', 'tasks.id', '=', 'task_documents.task_id')
            ->join('project_clients', 'project_clients.project_id', '=', 'tasks.project_id')
            ->where('project_clients.user_id', $user->id)
            ->where('tasks.organization_id', $organizationId)
            ->whereNull('tasks.deleted_at')
            ->pluck('task_documents.document_id')
            ->unique();

        return $documents->filter(fn (Document $document) => $document->access_level === DocumentAccessLevel::Public
            && $clientVisibleDocumentIds->contains($document->id))->pluck('id');
    }

    /**
     * task #73 phase 1 adds isClientInOrg() to the role check below —
     * "manage_documents stays tickable for Client" (unlike view_documents,
     * permanently locked off for that role) only means something once a
     * Client holding it can actually pass this gate. In practice this
     * only ever fires from the task edit page's inline add-document form
     * (its own $canManageDocuments is this exact check) — the standalone
     * Documents page/library stays unreachable for Client regardless,
     * since that's gated by view_documents, which Client can never hold.
     * Every document a Client creates this way is forced to Public
     * server-side (see DocumentUploadService::resolveAccessLevel())
     * before it ever reaches here, so this policy doesn't need its own
     * separate access-level restriction for the Client path.
     */
    public function create(User $user, int $organizationId): bool
    {
        if (! $user->hasPermission('manage_documents', $organizationId)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)
            || $user->isStaffInOrg($organizationId) || $user->isClientInOrg($organizationId);
    }

    /**
     * task #73 phase 2: rename, move, and change access level all share
     * this one gate, so the Documents-page edit button and its endpoint
     * can never disagree. Deliberately asymmetric with create() above: a
     * Client CAN create a document (with manage_documents), but can NEVER
     * edit or delete one afterward, even their own upload — checked first
     * and unconditionally, before manage_documents or ownership even
     * matter.
     */
    public function update(User $user, Document $document): bool
    {
        return $this->canManage($user, $document);
    }

    public function delete(User $user, Document $document): bool
    {
        return $this->canManage($user, $document);
    }

    private function canManage(User $user, Document $document): bool
    {
        if ($user->isClientInOrg($document->organization_id)) {
            return false;
        }

        if (! $user->hasPermission('manage_documents', $document->organization_id)) {
            return false;
        }

        if (! $this->view($user, $document)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($document->organization_id)
            || $document->uploaded_by === $user->id;
    }
}
