<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;

class LocationController extends BaseApiController
{
    public function divisions(): JsonResponse
    {
        $divisions = Division::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'name_bn' => $d->name_bangla ?? $d->name_bn ?? $d->name,
                'slug' => $d->slug,
                'districts_url' => url("/api/v1/locations/divisions/{$d->id}/districts"),
            ])
            ->values()
            ->all();

        return $this->success($divisions);
    }

    public function districts(int $divisionId): JsonResponse
    {
        $districts = District::query()
            ->where('division_id', $divisionId)
            ->orderBy('name')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'name_bn' => $d->name_bangla ?? $d->name_bn ?? $d->name,
                'slug' => $d->slug,
                'division_id' => $d->division_id,
                'upazilas_url' => url("/api/v1/locations/divisions/{$divisionId}/districts/{$d->id}/upazilas"),
            ])
            ->values()
            ->all();

        return $this->success($districts);
    }

    public function upazilas(int $divisionId, int $districtId): JsonResponse
    {
        $upazilas = Upazila::query()
            ->where('division_id', $divisionId)
            ->where('district_id', $districtId)
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'name_bn' => $u->name_bangla ?? $u->name_bn ?? $u->name,
                'slug' => $u->slug,
                'division_id' => $u->division_id,
                'district_id' => $u->district_id,
            ])
            ->values()
            ->all();

        return $this->success($upazilas);
    }

    public function all(): JsonResponse
    {
        $divisions = Division::query()
            ->with(['districts' => fn ($q) => $q->orderBy('name'), 'districts.upazilas' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'name_bn' => $d->name_bangla ?? $d->name_bn ?? $d->name,
                'slug' => $d->slug,
                'districts' => $d->districts->map(fn ($dist) => [
                    'id' => $dist->id,
                    'name' => $dist->name,
                    'name_bn' => $dist->name_bangla ?? $dist->name_bn ?? $dist->name,
                    'slug' => $dist->slug,
                    'division_id' => $dist->division_id,
                    'upazilas' => $dist->upazilas->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'name_bn' => $u->name_bangla ?? $u->name_bn ?? $u->name,
                        'slug' => $u->slug,
                        'division_id' => $u->division_id,
                        'district_id' => $u->district_id,
                    ])->values()->all(),
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return $this->success($divisions);
    }
}
