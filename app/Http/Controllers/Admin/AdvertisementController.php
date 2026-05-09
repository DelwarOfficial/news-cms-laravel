<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::latest()->paginate(20);
        return view('admin.advertisements.index', compact('ads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'position' => 'required',
            'type' => 'required|in:image,code',
        ]);

        Advertisement::create($validated);
        return back()->with('success', 'Advertisement created!');
    }
}