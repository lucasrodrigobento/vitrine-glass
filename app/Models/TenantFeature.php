<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantFeature extends Model
{
    protected $fillable = ['tenant_id', 'titulo', 'descricao', 'tipo', 'rota', 'imagens', 'ordem', 'ativo'];

    protected $casts = [
        'ativo'   => 'boolean',
        'imagens' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
