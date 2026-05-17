<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'language_id',
        'locale',
        'title',
        'slug',
        'summary',
        'body',
        'content',
        'meta_title',
        'meta_description',
        'status',
        'translation_method',
        'ai_provider',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
