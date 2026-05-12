<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Support\FrontendCache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $divisions = Division::query()
            ->with(['districts' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $districts = District::query()
            ->with('division')
            ->orderBy('name')
            ->get();

        $upazilas = Upazila::query()
            ->with(['division', 'district'])
            ->orderBy('name')
            ->paginate(50);

        return view('admin.locations.index', compact('divisions', 'districts', 'upazilas'));
    }

    public function storeDivision(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:divisions,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:divisions,slug'],
            'name_bangla' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:divisions,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = $this->locationPayload($validated);
        $payload['slug'] = $this->uniqueSlug(Division::class, $payload['slug']);

        Division::create($payload);

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'Division created successfully.');
    }

    public function storeDistrict(Request $request)
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('districts', 'slug')->where(fn ($query) => $query->where('division_id', $request->input('division_id'))),
            ],
            'name_bangla' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:districts,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = $this->locationPayload($validated);
        $payload['slug'] = $this->uniqueSlug(District::class, $payload['slug'], [
            'division_id' => $payload['division_id'],
        ]);

        District::create($payload);

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'District created successfully.');
    }

    public function storeUpazila(Request $request)
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('upazilas', 'slug')->where(fn ($query) => $query->where('district_id', $request->input('district_id'))),
            ],
            'name_bangla' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:upazilas,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $district = District::findOrFail($validated['district_id']);

        if ((int) $district->division_id !== (int) $validated['division_id']) {
            return back()->withInput()->with('error', 'Selected district does not belong to the selected division.');
        }

        $payload = $this->locationPayload($validated);
        $payload['slug'] = $this->uniqueSlug(Upazila::class, $payload['slug'], [
            'district_id' => $payload['district_id'],
        ]);

        Upazila::create($payload);

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'Upazila created successfully.');
    }

    public function destroyDivision(Division $division)
    {
        if ($division->posts()->exists() || $division->districts()->exists()) {
            return back()->with('error', 'Cannot delete a division with districts or posts.');
        }

        $division->delete();

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'Division deleted successfully.');
    }

    public function destroyDistrict(District $district)
    {
        if ($district->posts()->exists() || $district->upazilas()->exists()) {
            return back()->with('error', 'Cannot delete a district with upazilas or posts.');
        }

        $district->delete();

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'District deleted successfully.');
    }

    public function destroyUpazila(Upazila $upazila)
    {
        if ($upazila->posts()->exists()) {
            return back()->with('error', 'Cannot delete an upazila with posts.');
        }

        $upazila->delete();

        FrontendCache::flushLocations();

        return redirect()->route('admin.locations.index')->with('success', 'Upazila deleted successfully.');
    }

    private function locationPayload(array $validated): array
    {
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        return $validated;
    }

    /**
     * Keep admin entry forgiving while preserving scoped unique indexes.
     */
    private function uniqueSlug(string $modelClass, string $slug, array $scope = []): string
    {
        $base = $slug !== '' ? $slug : 'location';
        $candidate = $base;
        $suffix = 2;

        while (
            $modelClass::query()
                ->when($scope !== [], function ($query) use ($scope) {
                    foreach ($scope as $column => $value) {
                        $query->where($column, $value);
                    }
                })
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
