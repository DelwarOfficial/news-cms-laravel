<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Services\HomepageDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TickerController extends BaseApiController
{
    public function __construct(
        private readonly HomepageDataService $homepage,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);

        return $this->success(
            $this->homepage->getTicker($limit, app()->getLocale())
        );
    }
}
