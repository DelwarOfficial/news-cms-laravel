<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'name',
        'slug',
        'name_bangla',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function allDivisions(): array
    {
        try {
            if (Schema::hasTable('divisions')) {
                $divisions = Division::query()
                    ->active()
                    ->orderBy('name')
                    ->get(['name', 'name_bangla'])
                    ->mapWithKeys(fn (Division $division) => [
                        $division->name => $division->name_bangla ?: $division->name,
                    ])
                    ->all();

                if ($divisions !== []) {
                    return $divisions;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch divisions from database.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return collect(\App\Support\LocationDataProvider::getLocationData())
            ->mapWithKeys(fn (array $divisionData, string $division) => [
                $division => $divisionData['name_bn'] ?? $division,
            ])
            ->sortKeys()
            ->all();
    }

    public static function forDivision(string $division): array
    {
        try {
            if (Schema::hasTable('districts') && Schema::hasTable('divisions')) {
                $divisionId = Division::query()
                    ->where('name', $division)
                    ->orWhere('name_bangla', $division)
                    ->value('id');

                if ($divisionId) {
                    $districts = static::query()
                        ->where('division_id', $divisionId)
                        ->orderBy('name')
                        ->get(['name', 'name_bangla'])
                        ->map(fn (District $district) => [
                            'name' => $district->name,
                            'name_bangla' => $district->name_bangla ?: $district->name,
                        ])
                        ->all();

                    if ($districts !== []) {
                        return $districts;
                    }
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch districts from database.', [
                'division' => $division,
                'message' => $exception->getMessage(),
            ]);
        }

        return collect(\App\Support\LocationDataProvider::getLocationData()[$division]['districts'] ?? [])
            ->map(fn (array $districtData, string $district) => [
                'name' => $district,
                'name_bangla' => $districtData['name_bn'] ?? $district,
            ])
            ->sortBy('name')
            ->values()
            ->all();
    }

    public static function belongsToDivision(string $division, string $district): bool
    {
        return collect(static::forDivision($division))->contains(
            fn (array $item): bool => ($item['name'] ?? '') === $district
        );
    }
}
