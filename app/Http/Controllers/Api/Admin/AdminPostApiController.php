<?php

namespace App\Http\Controllers\Api\Admin;

use App\Jobs\ProcessPostPublishing;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPostApiController extends Controller
{
    public function store(Request $request)
    {
        // Authorization check using policy
        if ($request->user()->cannot('create', Post::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:500|unique:posts',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,published,scheduled,archived',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_photocard' => 'boolean',
        ]);

        try {
            $post = Post::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'status' => $validated['status'],
                'excerpt' => $validated['excerpt'] ?? null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'is_breaking' => $validated['is_breaking'] ?? false,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_trending' => $validated['is_trending'] ?? false,
                'is_photocard' => $validated['is_photocard'] ?? false,
                'user_id' => $request->user()->id,
                'language_id' => Language::idForLocale(app()->getLocale()),
            ]);

            if (!empty($validated['categories'])) {
                $post->categories()->sync($validated['categories']);
            }

            if ($validated['status'] === 'published') {
                ProcessPostPublishing::dispatch($post)->onQueue('publishing');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully',
                'data' => $post->load(Post::contentRelations())
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->cannot('update', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500|unique:posts,title,' . $post->id,
            'content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,pending,published,scheduled,archived',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_photocard' => 'boolean',
        ]);

        try {
            $post->update([
                'title' => $validated['title'] ?? $post->title,
                'content' => $validated['content'] ?? $post->content,
                'status' => $validated['status'] ?? $post->status,
                'excerpt' => $validated['excerpt'] ?? $post->excerpt,
                'meta_title' => $validated['meta_title'] ?? $post->meta_title,
                'meta_description' => $validated['meta_description'] ?? $post->meta_description,
                'is_breaking' => $validated['is_breaking'] ?? $post->is_breaking,
                'is_featured' => $validated['is_featured'] ?? $post->is_featured,
                'is_trending' => $validated['is_trending'] ?? $post->is_trending,
                'is_photocard' => $validated['is_photocard'] ?? $post->is_photocard,
            ]);

            $wasPublished = ($validated['status'] ?? $post->status) === 'published'
                && $post->status !== 'published';

            if (isset($validated['categories'])) {
                $post->categories()->sync($validated['categories'] ?? []);
            }

            if ($wasPublished) {
                ProcessPostPublishing::dispatch($post)->onQueue('publishing');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully',
                'data' => $post->load(Post::contentRelations())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->cannot('delete', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $post->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->cannot('publish', Post::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,pending,published,scheduled,archived'
        ]);

        try {
            $oldStatus = $post->status;
            $post->update(['status' => $validated['status']]);

            if ($validated['status'] === 'published' && $oldStatus !== 'published') {
                ProcessPostPublishing::dispatch($post)->onQueue('publishing');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post status updated',
                'data' => $post->load(Post::contentRelations())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update post status: ' . $e->getMessage()
            ], 500);
        }
    }
}
