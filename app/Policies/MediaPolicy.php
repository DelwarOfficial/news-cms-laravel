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
        // Editors/admins can manage any media; contributors/authors only their own.
        if ($user->hasRole(['Super Admin', 'Admin', 'Editor'])) {
            return $user->can('media.manage');
        }

        return (int) $media->user_id === (int) $user->id;
    }
}
