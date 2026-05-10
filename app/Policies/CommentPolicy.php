<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('comments.manage');
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->can('comments.manage');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->can('comments.manage');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->can('comments.manage');
    }
}
