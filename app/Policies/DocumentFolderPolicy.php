<?php

namespace App\Policies;

use App\Models\DocumentFolder;
use App\Models\User;

/**
 * Folders have no visibility of their own (see DocumentFolder's own
 * docblock) — there is no view()/viewAny() here on purpose; everyone
 * holding view_documents in the company sees every folder, gated at the
 * controller/index level the same way the Documents page itself already
 * is, not per-folder.
 */
class DocumentFolderPolicy
{
    /**
     * Creating a folder needs only manage_documents in the company — no
     * creator/management-tier restriction, unlike rename/delete below.
     * The parent folder (or company root) itself needs no rights check;
     * only that it belongs to the same company, which the controller
     * validates from server-side context, never the request body.
     */
    public function create(User $user, int $organizationId): bool
    {
        return $user->hasPermission('manage_documents', $organizationId);
    }

    public function update(User $user, DocumentFolder $folder): bool
    {
        return $this->canManage($user, $folder);
    }

    public function delete(User $user, DocumentFolder $folder): bool
    {
        return $this->canManage($user, $folder);
    }

    /**
     * Shared by rename and delete: manage_documents AND (the folder's own
     * creator OR management in that company) — owner/super_admin always.
     * A single method so the rename/delete buttons and their endpoints
     * can never disagree, same convention as
     * CommentPolicy::canEditOwnComments()/DocumentPolicy's shared manage
     * check.
     */
    private function canManage(User $user, DocumentFolder $folder): bool
    {
        if (! $user->hasPermission('manage_documents', $folder->organization_id)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner()
            || $user->isManagementInOrg($folder->organization_id)
            || $folder->created_by === $user->id;
    }
}
