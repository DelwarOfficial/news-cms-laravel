<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Support\FrontendCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FrontendLocationImportSeeder extends Seeder
{
    public function run(): void
    {
        $locationData = $this->locationData();
        $bnMap = $this->upazilaBanglaMap();

        DB::transaction(function () use ($locationData, $bnMap): void {
            foreach ($locationData as $divisionName => $divisionData) {
                $division = Division::updateOrCreate(
                    ['slug' => $this->slug($divisionName)],
                    [
                        'name' => $divisionName,
                        'name_bangla' => Arr::get($divisionData, 'name_bn'),
                        'is_active' => true,
                    ]
                );

                foreach (Arr::get($divisionData, 'districts', []) as $districtName => $districtData) {
                    $district = District::updateOrCreate(
                        [
                            'division_id' => $division->id,
                            'slug' => $this->slug($districtName),
                        ],
                        [
                            'name' => $districtName,
                            'name_bangla' => Arr::get($districtData, 'name_bn'),
                            'is_active' => true,
                        ]
                    );

                    foreach (Arr::get($districtData, 'upazilas', []) as $upazilaName) {
                        Upazila::updateOrCreate(
                            [
                                'district_id' => $district->id,
                                'slug' => $this->slug($upazilaName),
                            ],
                            [
                                'division_id' => $division->id,
                                'name' => $upazilaName,
                                'name_bangla' => $bnMap[$upazilaName] ?? $upazilaName,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        });

        FrontendCache::flushLocations();
    }

    private function locationData(): array
    {
        $path = $this->firstExistingPath([
            env('FRONTEND_LOCATION_DATA_PATH'),
            dirname(base_path(), 2).DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'dhaka-magazine'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'bangladesh-locations.json',
            resource_path('data/bangladesh-locations.json'),
        ]);

        if (! $path) {
            return [];
        }

        return json_decode((string) file_get_contents($path), true) ?: [];
    }

    private function upazilaBanglaMap(): array
    {
        $path = $this->firstExistingPath([
            env('FRONTEND_UPAZILA_BN_MAP_PATH'),
            dirname(base_path(), 2).DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'dhaka-magazine'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'upazila-name-bn-map.php',
            resource_path('data/upazila-name-bn-map.php'),
        ]);

        return $path ? (require $path) : [];
    }

    private function firstExistingPath(array $paths): ?string
    {
        foreach (array_filter($paths) as $path) {
            if (is_string($path) && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function slug(string $value): string
    {
        return Str::slug($value) ?: Str::lower(Str::of($value)->replace(' ', '-')->toString());
    }
}
