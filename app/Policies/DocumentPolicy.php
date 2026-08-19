<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * A minimal policy scoped to what task-document linking needs. The full
 * Documents phase (its own management page, more granular access rules)
 * hasn't been built yet — see CLAUDE.md phase 6 — so this only covers
 * enough to browse/attach/create documents from within a task.
 */
class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return match ($document->access_level) {
            'public' => in_array($document->organization_id, $user->visibleOrganizationIds(), true),
            'internal' => in_array($document->organization_id, $user->visibleOrganizationIds(), true),
            'private' => $document->uploaded_by === $user->id || $user->isManagementInOrg($document->organization_id),
            default => false,
        };
    }

    public function create(User $user, int $organizationId): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return in_array($organizationId, $user->visibleOrganizationIds(), true);
    }
}
