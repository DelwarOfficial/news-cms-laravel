<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
        $widgets = Widget::orderBy('area')->orderBy('order')->get();
        return view('admin.widgets.index', compact('widgets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area' => 'required',
            'type' => 'required',
            'title' => 'required',
        ]);

        Widget::create($validated);
        return back()->with('success', 'Widget created!');
    }
}