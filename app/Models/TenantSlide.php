<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSlide extends Model
{
    protected $fillable = [
        'tenant_id', 'path', 'legenda', 'ordem', 'ativo',
        'titulo', 'subtitulo', 'botao_label', 'botao_url',
    ];

    protected $casts = ['ativo' => 'boolean'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
