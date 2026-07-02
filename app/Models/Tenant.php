<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    protected $fillable = [
        'slug', 'nome', 'dominio', 'ativo',
        'cor_primaria', 'cor_secundaria', 'cor_texto', 'cor_rodape_fundo', 'cor_rodape_links',
        'font_body', 'font_heading', 'font_accent', 'font_google_url',
        'logo', 'favicon', 'og_image', 'logo_header',
        'whatsapp', 'whatsapp_exibir', 'email',
        'instagram', 'facebook', 'endereco', 'areas_atendidas',
        'google_ads_id', 'google_maps_embed', 'schema_type',
        'google_drive_api_key', 'google_drive_folder_id',
        'seo_title', 'seo_description', 'seo_keywords',
        'hero_titulo', 'hero_subtitulo',
        'sobre_titulo', 'sobre_descricao', 'sobre_missao', 'sobre_visao', 'sobre_valores',
        'menu_servicos_label', 'menu_catalogo_visivel', 'menu_catalogo_label', 'menu_catalogo_posicao',
    ];

    protected $casts = [
        'ativo'                   => 'boolean',
        'areas_atendidas'         => 'array',
        'menu_catalogo_visivel'   => 'boolean',
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
        // todos ativos (para config/rotas de serviço)
        $activeServices = $this->services->where('ativo', true)->values();
        // apenas os que aparecem no menu — null seguro: coluna ausente não exclui o serviço
        $menuServices   = $activeServices->filter(fn($s) => ($s->mostrar_menu ?? true) !== false)->values();
        $activeFeatures = $this->features->where('ativo', true)->values();

        return [
            'id'              => $this->id,
            'slug'            => $this->slug,
            'nome'            => $this->nome,
            'dominio'         => $this->dominio,
            'cor_primaria'    => $this->cor_primaria,
            'cor_secundaria'  => $this->cor_secundaria,
            'cor_texto'        => $this->cor_texto        ?: '#6f7070',
            'cor_rodape_fundo' => $this->cor_rodape_fundo ?: null,
            'cor_rodape_links' => $this->cor_rodape_links ?: null,
            'fonts' => [
                'body'       => $this->font_body       ?? 'Open Sans',
                'heading'    => $this->font_heading    ?? 'Righteous',
                'accent'     => $this->font_accent     ?? 'Josefin Sans',
                'google_url' => $this->font_google_url ?? 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Righteous&family=Josefin+Sans&display=swap',
            ],
            'whatsapp'        => $this->whatsapp,
            'whatsapp_exibir' => $this->whatsapp_exibir,
            'email'           => $this->email,
            'instagram'       => $this->instagram,
            'facebook'        => $this->facebook,
            'google_ads_id'   => $this->google_ads_id,
            'google_maps_embed' => $this->google_maps_embed,
            'logo'            => $this->logo ? Storage::url($this->logo) : '/images/logo-default.png',
            'logo_header'     => $this->logo_header ?? 'logo',
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
            'menu' => $this->buildMenu($menuServices),
        ];
    }

    private function buildMenu($activeServices): array
    {
        $servicosLabel  = $this->menu_servicos_label    ?: 'Serviços';
        $catVisivel     = $this->menu_catalogo_visivel  ?? true;
        $catLabel       = $this->menu_catalogo_label    ?: 'Catálogo de Serviços';
        $catPosicao     = $this->menu_catalogo_posicao  ?: 'dropdown';

        $catalogoItem = $catVisivel
            ? ['tipo' => 'pagina', 'rota' => 'catalogo', 'label' => $catLabel, 'ativo' => true]
            : null;

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

            if ($catalogoItem && $catPosicao === 'dropdown') {
                $filhos[] = $catalogoItem;
            }

            $menu[] = ['tipo' => 'dropdown', 'label' => $servicosLabel, 'ativo' => true, 'filhos' => $filhos];
        } elseif ($catalogoItem && $catPosicao === 'dropdown') {
            // sem serviços: catálogo vira item direto com o label de serviços
            $menu[] = ['tipo' => 'pagina', 'rota' => 'catalogo', 'label' => $servicosLabel, 'ativo' => true];
            $catalogoItem = null; // já consumido
        }

        // Catálogo como item de topo (fora do dropdown de serviços)
        if ($catalogoItem && $catPosicao === 'topo') {
            $menu[] = $catalogoItem;
        }

        $menu[] = ['tipo' => 'pagina', 'rota' => 'contato', 'label' => 'Contato', 'ativo' => true];

        return $menu;
    }
}
