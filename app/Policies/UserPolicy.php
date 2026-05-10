<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('users.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $target): bool
    {
        if ($user->can('roles.manage')) {
            return true;
        }

        return $user->can('users.manage') && ! $target->hasRole('Super Admin');
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        if ($user->can('roles.manage')) {
            return true;
        }

        return $user->can('users.manage') && ! $target->hasRole('Super Admin');
    }
}
