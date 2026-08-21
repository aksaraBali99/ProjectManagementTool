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

    public function create(User $user, int $organizationId): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        if (! $user->hasPermission('manage_documents', $organizationId)) {
            return false;
        }

        return $user->isManagementInOrg($organizationId);
    }
}
