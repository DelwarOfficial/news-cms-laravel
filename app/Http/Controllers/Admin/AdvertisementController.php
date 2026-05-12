<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    protected $allowedPositions = [
        'header',
        'sidebar',
        'content_top',
        'content_bottom',
        'footer',
        'category-top',
        'category-in-feed',
        'category-bottom',
        'sidebar-rectangle-1',
        'sidebar-half-page',
        'sidebar-rectangle-2',
    ];
    protected $allowedTypes = ['image', 'code'];

    public function index()
    {
        $ads = Advertisement::latest()->paginate(20);
        
        return view('admin.advertisements.index', compact('ads'));
    }

    public function create()
    {
        $this->authorize('create', Advertisement::class);
        
        $positions = $this->allowedPositions;
        $types = $this->allowedTypes;
        
        return view('admin.advertisements.create', compact('positions', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Advertisement::class);
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'position' => 'required|in:' . implode(',', $this->allowedPositions),
            'type' => 'required|in:' . implode(',', $this->allowedTypes),
            'image' => 'required_if:type,image|nullable|image|max:5120',
            'code' => 'required_if:type,code|nullable|max:5000',
            'url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            $advertisement = new Advertisement();
            $advertisement->title = $validated['title'];
            $advertisement->position = $validated['position'];
            $advertisement->type = $validated['type'];
            $advertisement->url = $validated['url'] ?? null;
            $advertisement->start_date = $validated['start_date'] ?? null;
            $advertisement->end_date = $validated['end_date'] ?? null;
            $advertisement->is_active = $request->boolean('is_active', true);

            if ($validated['type'] === 'image' && $request->hasFile('image')) {
                $path = $request->file('image')->store('advertisements', 'public');
                $advertisement->image = $path;
            } elseif ($validated['type'] === 'code') {
                $advertisement->code = $validated['code'] ?? null;
            }

            $advertisement->save();

            return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement created successfully!');
        } catch (\Exception $e) {
            Log::error('Advertisement creation failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create advertisement: ' . $e->getMessage());
        }
    }

    public function edit(Advertisement $advertisement)
    {
        $this->authorize('update', $advertisement);
        
        $positions = $this->allowedPositions;
        $types = $this->allowedTypes;
        
        return view('admin.advertisements.edit', compact('advertisement', 'positions', 'types'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $this->authorize('update', $advertisement);
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'position' => 'required|in:' . implode(',', $this->allowedPositions),
            'type' => 'required|in:' . implode(',', $this->allowedTypes),
            'image' => 'nullable|image|max:5120',
            'code' => 'nullable|max:5000',
            'url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            $advertisement->title = $validated['title'];
            $advertisement->position = $validated['position'];
            $advertisement->type = $validated['type'];
            $advertisement->url = $validated['url'] ?? null;
            $advertisement->start_date = $validated['start_date'] ?? null;
            $advertisement->end_date = $validated['end_date'] ?? null;
            $advertisement->is_active = $request->boolean('is_active', true);

            if ($validated['type'] === 'image') {
                if ($request->hasFile('image')) {
                    // Delete old image
                    if ($advertisement->image) {
                        Storage::disk('public')->delete($advertisement->image);
                    }
                    $path = $request->file('image')->store('advertisements', 'public');
                    $advertisement->image = $path;
                }
                $advertisement->code = null;
            } elseif ($validated['type'] === 'code') {
                if ($advertisement->image) {
                    Storage::disk('public')->delete($advertisement->image);
                }
                $advertisement->image = null;
                $advertisement->code = $validated['code'] ?? null;
            }

            $advertisement->save();

            return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement updated successfully!');
        } catch (\Exception $e) {
            Log::error('Advertisement update failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update advertisement: ' . $e->getMessage());
        }
    }

    public function destroy(Advertisement $advertisement)
    {
        $this->authorize('delete', $advertisement);
        
        try {
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            $advertisement->delete();
            return back()->with('success', 'Advertisement deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Advertisement deletion failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete advertisement: ' . $e->getMessage());
        }
    }
}
