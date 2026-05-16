<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Services\HomepageDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatedController extends BaseApiController
{
    public function __construct(
        private readonly HomepageDataService $homepage,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $postId = (int) $request->get('post_id');
        $limit = min((int) $request->get('limit', 4), 20);

        if (! $postId) {
            return $this->success([]);
        }

        return $this->success(
            $this->homepage->getRelated($postId, $limit, app()->getLocale())
        );
    }
}
