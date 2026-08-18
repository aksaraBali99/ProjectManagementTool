<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }
}
