<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('settings.manage');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->can('settings.manage');
    }

    public function update(User $user): bool
    {
        return $user->can('settings.manage');
    }
}
