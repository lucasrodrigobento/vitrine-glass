<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    protected $fillable = ['tenant_id', 'tenant_slug', 'categoria', 'path', 'titulo', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
