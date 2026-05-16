<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\Api\V1\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MediaController extends BaseApiController
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('limit', 20), 50);
        $cacheKey = 'v1:media:' . md5(json_encode($request->all()));

        $media = Cache::remember($cacheKey, 600, function () use ($perPage, $request) {
            $query = Media::query()->orderByDesc('created_at');

            if ($request->filled('file_type')) {
                $query->where('file_type', 'like', $request->file_type . '%');
            }

            return $query->paginate($perPage);
        });

        return $this->paginated(MediaResource::collection($media));
    }

    public function show(int $id)
    {
        $media = Cache::remember("v1:media:{$id}", 600, function () use ($id) {
            return Media::find($id);
        });

        if (! $media) {
            return $this->error('Not Found', 'Media not found.', 404);
        }

        return $this->success(new MediaResource($media));
    }
}
