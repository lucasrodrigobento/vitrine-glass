<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantService extends Model
{
    protected $fillable = ['tenant_id', 'slug', 'titulo', 'descricao', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
