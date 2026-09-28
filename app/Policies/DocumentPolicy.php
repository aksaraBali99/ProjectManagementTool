<?php

namespace App\Policies;

use App\Enums\DocumentAccessLevel;
use App\Models\Document;
use App\Models\User;

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
}
