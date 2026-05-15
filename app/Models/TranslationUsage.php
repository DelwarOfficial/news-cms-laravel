<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationUsage extends Model
{
    protected $fillable = [
        'post_id',
        'from_locale',
        'to_locale',
        'character_count',
        'cost_estimate',
        'status',
    ];

    public function post()
    {
        $this->belongsTo(Post::class);
    }
}
