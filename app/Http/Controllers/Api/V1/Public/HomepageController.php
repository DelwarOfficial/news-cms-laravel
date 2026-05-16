<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Services\HomepageDataService;
use Illuminate\Http\JsonResponse;

class HomepageController extends BaseApiController
{
    public function __construct(
        private readonly HomepageDataService $homepage,
    ) {
    }

    public function index(): JsonResponse
    {
        $sections = config('homepage.sections', []);
        $locale = app()->getLocale();

        $data = $this->homepage->get($sections, $locale);

        return $this->success($data);
    }

    public function refresh(): JsonResponse
    {
        $this->homepage->flush();

        return $this->success(['message' => 'Homepage cache cleared.']);
    }

    public function categories(): JsonResponse
    {
        return $this->success($this->homepage->getCategories());
    }
}
