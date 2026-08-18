<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function view(User $user, Department $department): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function update(User $user, Department $department): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->isSuperAdmin() || $user->isOwner();
    }
}
