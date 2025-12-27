<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->is_admin;
    }

    public function restore(User $user, Branch $branch): bool
    {
        return $user->is_admin;
    }

    public function forceDelete(User $user, Branch $branch): bool
    {
        return $user->is_admin;
    }
}
