<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommentController extends BaseApiController
{
    public function index(Request $request, int $postId)
    {
        $post = Post::find($postId);

        if (! $post) {
            return $this->error('Not Found', 'Post not found.', 404);
        }

        $perPage = min((int) $request->get('limit', 20), 50);
        $cacheKey = "v1:posts:{$postId}:comments:" . md5(json_encode($request->all()));

        $comments = Cache::remember($cacheKey, 120, function () use ($post, $perPage) {
            return Comment::with('replies')
                ->where('post_id', $post->id)
                ->where('status', 'approved')
                ->whereNull('parent_id')
                ->orderByDesc('created_at')
                ->paginate($perPage);
        });

        return $this->paginated(CommentResource::collection($comments));
    }
}
