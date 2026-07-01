<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    protected $fillable = [
        'slug', 'nome', 'dominio', 'ativo',
        'cor_primaria', 'cor_secundaria',
        'logo', 'favicon', 'og_image',
        'whatsapp', 'whatsapp_exibir', 'email',
        'instagram', 'facebook', 'endereco', 'areas_atendidas',
        'google_ads_id', 'google_maps_embed', 'schema_type',
        'seo_title', 'seo_description', 'seo_keywords',
        'hero_titulo', 'hero_subtitulo',
        'sobre_titulo', 'sobre_descricao', 'sobre_missao', 'sobre_visao', 'sobre_valores',
    ];

    protected $casts = [
        'ativo'           => 'boolean',
        'areas_atendidas' => 'array',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(TenantService::class)->orderBy('ordem');
    }

    public function slides(): HasMany
    {
        return $this->hasMany(TenantSlide::class)->orderBy('ordem');
    }

    public function features(): HasMany
    {
        return $this->hasMany(TenantFeature::class)->orderBy('ordem');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }

    public function toConfigArray(): array
    {
        $activeServices = $this->services->where('ativo', true)->values();
        $activeFeatures = $this->features->where('ativo', true)->values();

        return [
            'id'              => $this->id,
            'slug'            => $this->slug,
            'nome'            => $this->nome,
            'dominio'         => $this->dominio,
            'cor_primaria'    => $this->cor_primaria,
            'cor_secundaria'  => $this->cor_secundaria,
            'whatsapp'        => $this->whatsapp,
            'whatsapp_exibir' => $this->whatsapp_exibir,
            'email'           => $this->email,
            'instagram'       => $this->instagram,
            'facebook'        => $this->facebook,
            'google_ads_id'   => $this->google_ads_id,
            'google_maps_embed' => $this->google_maps_embed,
            'logo'            => $this->logo ? Storage::url($this->logo) : '/images/logo-default.png',
            'favicon'         => $this->favicon ? Storage::url($this->favicon) : '/favicon.ico',
            'og_image'        => $this->og_image ? Storage::url($this->og_image) : '/images/og-default.jpg',
            'endereco'        => $this->endereco ?? '',
            'areas_atendidas' => $this->areas_atendidas ?? [],
            'schema_type'     => $this->schema_type,
            'seo' => [
                'title_padrao' => $this->seo_title ?? $this->nome,
                'description'  => $this->seo_description ?? '',
                'keywords'     => $this->seo_keywords ?? '',
            ],
            'paginas' => [
                'home' => [
                    'titulo_hero'    => $this->hero_titulo ?? $this->nome,
                    'subtitulo_hero' => $this->hero_subtitulo ?? '',
                ],
                'sobre' => [
                    'titulo'    => $this->sobre_titulo ?? $this->nome,
                    'descricao' => $this->sobre_descricao ?? '',
                    'missao'    => $this->sobre_missao ?? '',
                    'visao'     => $this->sobre_visao ?? '',
                    'valores'   => $this->sobre_valores ?? '',
                ],
            ],
            'servicos' => $activeServices->map(fn($s) => [
                'slug'   => $s->slug,
                'titulo' => $s->titulo,
                'ativo'  => true,
            ])->toArray(),
            'features' => $activeFeatures->map(fn($f) => [
                'titulo'    => $f->titulo,
                'descricao' => $f->descricao ?? '',
                'imagens'   => $f->imagens ?? [],
                'tipo'      => $f->tipo,
                'rota'      => $f->rota,
            ])->toArray(),
            'menu' => $this->buildMenu($activeServices),
        ];
    }

    private function buildMenu($activeServices): array
    {
        $menu = [
            ['tipo' => 'pagina', 'rota' => 'home',  'label' => 'Início',  'ativo' => true],
            ['tipo' => 'pagina', 'rota' => 'sobre', 'label' => 'Empresa', 'ativo' => true],
        ];

        if ($activeServices->isNotEmpty()) {
            $filhos = $activeServices->map(fn($s) => [
                'tipo'  => 'servico',
                'rota'  => $s->slug,
                'label' => $s->titulo,
                'ativo' => true,
            ])->toArray();

            $filhos[] = ['tipo' => 'pagina', 'rota' => 'catalogo', 'label' => 'Catálogo', 'ativo' => true];

            $menu[] = ['tipo' => 'dropdown', 'label' => 'Serviços', 'ativo' => true, 'filhos' => $filhos];
        } else {
            $menu[] = ['tipo' => 'pagina', 'rota' => 'catalogo', 'label' => 'Serviços', 'ativo' => true];
        }

        $menu[] = ['tipo' => 'pagina', 'rota' => 'contato', 'label' => 'Contato', 'ativo' => true];

        return $menu;
    }
}
