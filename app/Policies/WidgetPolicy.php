<?php

namespace App\Policies;

use App\Models\Widget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WidgetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('menus.manage');
    }

    public function view(User $user, Widget $widget): bool
    {
        return $user->can('menus.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('menus.manage');
    }

    public function update(User $user, Widget $widget): bool
    {
        return $user->can('menus.manage');
    }

    public function delete(User $user, Widget $widget): bool
    {
        return $user->can('menus.manage');
    }
}
