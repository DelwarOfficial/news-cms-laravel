<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'menu_id', 'parent_id', 'title', 'url', 'target', 'order', 'type', 'reference_id'];

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
