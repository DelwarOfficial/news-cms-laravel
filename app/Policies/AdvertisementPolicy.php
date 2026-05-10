<?php

namespace App\Policies;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertisementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ads.manage');
    }

    public function view(User $user, Advertisement $advertisement): bool
    {
        return $user->can('ads.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('ads.manage');
    }

    public function update(User $user, Advertisement $advertisement): bool
    {
        return $user->can('ads.manage');
    }

    public function delete(User $user, Advertisement $advertisement): bool
    {
        return $user->can('ads.manage');
    }
}
