<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminCommentApiController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('comments.manage')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = Comment::with(['post:id,title', 'user:id,name'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json(['status' => 'success', 'data' => $comments]);
    }

    public function status(Request $request, $id)
    {
        if (!$request->user()->can('comments.manage')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,spam,trash'
        ]);

        $comment->update(['status' => $validated['status']]);

        return response()->json(['status' => 'success', 'data' => $comment]);
    }
}
