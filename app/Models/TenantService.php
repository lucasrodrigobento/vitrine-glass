<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantService extends Model
{
    protected $fillable = ['tenant_id', 'slug', 'titulo', 'descricao', 'ordem', 'ativo', 'mostrar_menu'];

    protected $casts = [
        'ativo'        => 'boolean',
        'mostrar_menu' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
