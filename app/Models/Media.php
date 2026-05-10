<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['user_id', 'folder_id', 'filename', 'original_name', 'disk', 'path', 'url', 'mime_type', 'size', 'alt_text', 'caption'];
}
