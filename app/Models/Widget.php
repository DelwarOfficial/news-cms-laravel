<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['tenant_id', 'area', 'type', 'title', 'content', 'config', 'order', 'is_active'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];
}