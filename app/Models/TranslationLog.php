<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TranslationLog extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'provider_id',
        'provider_name',
        'model',
        'from_locale',
        'to_locale',
        'input_tokens',
        'output_tokens',
        'total_chars',
        'cost_usd',
        'duration_ms',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'cost_usd' => 'float',
        ];
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
