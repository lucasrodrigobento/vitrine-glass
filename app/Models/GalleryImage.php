<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['tenant_slug', 'categoria', 'path', 'titulo', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];
}
