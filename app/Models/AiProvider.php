<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'driver_class',
        'api_key',
        'endpoint',
        'model',
        'options',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function translationLogs(): HasMany
    {
        return $this->hasMany(TranslationLog::class, 'provider_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function resolveDriver(): \App\Translation\Contracts\TranslationProvider
    {
        $class = $this->driver_class;

        return new $class($this);
    }
}
