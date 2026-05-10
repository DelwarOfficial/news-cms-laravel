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
        return $user->can('dashboard.view');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('posts.edit.any')
            || ($user->can('posts.edit.own') && $user->id === $post->user_id);
    }

    public function create(User $user): bool
    {
        return $user->can('posts.create');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('posts.edit.any')
            || ($user->can('posts.edit.own') && $user->id === $post->user_id);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('posts.delete.any')
            || ($user->can('posts.delete.own') && $user->id === $post->user_id);
    }

    public function publish(User $user): bool
    {
        return $user->can('posts.publish');
    }

    public function submitForReview(User $user): bool
    {
        return $user->can('posts.submit_review');
    }
}
