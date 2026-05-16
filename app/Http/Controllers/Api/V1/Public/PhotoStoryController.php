<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Services\HomepageDataService;
use Illuminate\Http\JsonResponse;

class PhotoStoryController extends BaseApiController
{
    public function __construct(
        private readonly HomepageDataService $homepage,
    ) {
    }

    public function index(): JsonResponse
    {
        return $this->success(
            $this->homepage->getPhotoStoryData(app()->getLocale())
        );
    }
}
