<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\HomeDataService;

class HomeController extends Controller
{
    public function index(HomeDataService $homeDataService)
    {
        return view('pages.home', $homeDataService->getHomepageData());
    }

    public function photoStoryData(HomeDataService $homeDataService)
    {
        return response()->json($homeDataService->getPhotoStoryData());
    }
}
