<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\CmsStorePostRequest;
use App\Http\Requests\Api\V1\CmsUpdatePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Services\CmsPostService;
use Illuminate\Http\JsonResponse;

class PostController extends BaseApiController
{
    public function __construct(
        private readonly CmsPostService $cmsPostService,
    ) {}

    public function store(CmsStorePostRequest $request): JsonResponse
    {
        $userId = $request->get('api_key_owner') ?: 1;

        $post = $this->cmsPostService->create($request->validated(), $userId);

        $post->load('author', 'categories', 'tags', 'featuredMedia', 'primaryCategory');

        return $this->created(new PostResource($post));
    }

    public function update(CmsUpdatePostRequest $request, $id): JsonResponse
    {
        $post = Post::withTrashed()->findOrFail($id);

        $post = $this->cmsPostService->update($post, $request->validated());

        $post->load('author', 'categories', 'tags', 'featuredMedia', 'primaryCategory');

        return $this->success(new PostResource($post));
    }

    public function destroy($id): JsonResponse
    {
        $post = Post::findOrFail($id);

        $this->cmsPostService->destroy($post);

        return $this->noContent();
    }
}
