<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function view(User $user, Role $role): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function update(User $user, Role $role): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }
}
