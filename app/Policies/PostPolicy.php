<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        if ($user->hasRole(['Super Admin', 'Admin', 'Editor'])) {
            return true;
        }
        return $user->id === $post->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->hasRole(['Super Admin', 'Admin', 'Editor'])) {
            return true;
        }
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return true;
        }
        return $user->id === $post->user_id;
    }

    public function publish(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Admin', 'Editor']);
    }
}