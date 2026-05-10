<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPostApiController extends Controller
{
    public function store(Request $request)
    {
        // Add authorization check (requires policies, using gate directly)
        if (!$request->user()->can('posts.create')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,published,scheduled,archived',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $post = new Post($validated);
        $post->user_id = $request->user()->id;
        $post->slug = Str::slug($validated['title']);
        $post->save();

        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        return response()->json(['status' => 'success', 'data' => $post], 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (!$request->user()->can('posts.edit.any') && ($request->user()->cannot('posts.edit.own') || $post->user_id !== $request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'string|max:500',
            'content' => 'string',
        ]);

        $post->update($validated);

        return response()->json(['status' => 'success', 'data' => $post]);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (!$request->user()->can('posts.delete.any') && ($request->user()->cannot('posts.delete.own') || $post->user_id !== $request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['status' => 'success', 'message' => 'Post deleted successfully']);
    }

    public function status(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (!$request->user()->can('posts.publish')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,pending,published,scheduled,archived'
        ]);

        $post->update(['status' => $validated['status']]);

        return response()->json(['status' => 'success', 'data' => $post]);
    }
}
