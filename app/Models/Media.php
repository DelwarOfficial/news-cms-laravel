<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;
    protected $fillable = [
        'folder_id',
        'user_id',
        'name',
        'file_name',
        'file_path',
        'file_url',
        'file_type',
        'file_size',
        'width',
        'height',
        'alt_text',
        'caption',
        'credit',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Column names updated to match frontend schema
    public function getUrlAttribute(): ?string
    {
        return $this->file_url ?? $this->attributes['url'] ?? null;
    }
}
