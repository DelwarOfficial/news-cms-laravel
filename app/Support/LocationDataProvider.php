<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class LocationDataProvider
{
    public static function getUpazilaBnMap(): array
    {
        return Cache::rememberForever('upazila_bn_map', function () {
            $bnMapPath = resource_path('data/upazila-name-bn-map.php');
            return file_exists($bnMapPath) ? (require $bnMapPath) : [];
        });
    }

    public static function getLocationData(): array
    {
        return Cache::rememberForever('bangladesh_location_data', function () {
            $path = resource_path('data/bangladesh-locations.json');
            return file_exists($path) ? (json_decode(file_get_contents($path), true) ?: []) : [];
        });
    }
}
