<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('media.manage');
    }

    public function view(User $user, Media $media): bool
    {
        return $user->can('media.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('media.manage');
    }

    public function update(User $user, Media $media): bool
    {
        return $user->can('media.manage');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->can('media.manage');
    }
}
