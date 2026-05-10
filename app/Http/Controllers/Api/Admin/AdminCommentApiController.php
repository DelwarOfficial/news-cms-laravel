<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminCommentApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Comment::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'status' => 'nullable|in:pending,approved,rejected,spam',
        ]);

        $query = Comment::with(['post:id,title', 'user:id,name']);
        
        if ($request->has('status')) {
            $query->where('status', $validated['status']);
        }

        $comments = $query->latest()->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'status' => 'success',
            'data' => $comments
        ]);
    }

    public function status(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($request->user()->cannot('update', $comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,spam'
        ]);

        try {
            $comment->update(['status' => $validated['status']]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment status updated successfully',
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update comment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($request->user()->cannot('delete', $comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $comment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
