<?php

namespace App\Enums;

/**
 * Why User::boardOrganizationIds() came up empty for a given user — lets
 * the Dashboard/Kanban empty state point at the actual cause instead of one
 * generic message regardless of reason.
 */
enum BoardAccessDeniedReason
{
    case PermissionDenied;
    case NoProject;
    case NoAccess;

    public function message(): string
    {
        return match ($this) {
            self::PermissionDenied => 'You have no access for this page.',
            self::NoProject => "You don't have any active project yet.",
            self::NoAccess => "You don't have access to any companies yet.",
        };
    }
}
