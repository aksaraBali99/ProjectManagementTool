<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }
}
