<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\HomeDataService;

class HomeController extends Controller
{
    public function index(HomeDataService $homeDataService)
    {
        $locale = app()->getLocale();

        return view('pages.home', array_merge(
            $homeDataService->getHomepageData(),
            ['locale' => $locale],
        ));
    }

    public function photoStoryData(HomeDataService $homeDataService)
    {
        return response()->json($homeDataService->getPhotoStoryData());
    }
}
