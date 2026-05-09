<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    public function update(User $user, User $target): bool
    {
        if ($user->hasRole('Super Admin')) return true;
        if ($user->hasRole('Admin') && !$target->hasRole('Super Admin')) return true;
        return false;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole('Super Admin') && $user->id !== $target->id;
    }
}