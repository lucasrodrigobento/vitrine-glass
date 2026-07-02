<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\Tenant;
use App\Models\TenantFeature;
use App\Models\TenantService;
use App\Models\TenantSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TenantSeeder extends Seeder
{
    private string $taprootProjects;

    public function __construct()
    {
        $this->taprootProjects = env('TAPROOT_PROJECTS_PATH', '/mnt/c/taproot/projects');
    }

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('  Vitrine Glass — Seeder de Tenants');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');

        $this->seedLiderVidros();
        $this->seedBoxVidros();

        $this->command->info('');
        $this->command->info('✅ Seeder concluído com sucesso.');
        $this->command->info('');
    }

    // ─────────────────────────────────────────────────
    //  LÍDER VIDROS
    // ─────────────────────────────────────────────────

    private function seedLiderVidros(): void
    {
        $this->command->info('▶ Líder Vidros...');

        $source = "{$this->taprootProjects}/lider-vidros";

        $tenant = Tenant::updateOrCreate(
            ['slug' => 'lider-vidros'],
            [
                'nome'            => 'Líder Vidros',
                'dominio'         => 'lidervidros.com.br',
                'ativo'           => true,
                'cor_primaria'    => '#ed3237',
                'cor_secundaria'  => '#9e2016',
                'whatsapp'        => '5562983004326',
                'whatsapp_exibir' => '(62) 9 8300-4326',
                'email'           => 'comercial@lidervidros.com.br',
                'instagram'       => 'liderdosvidros',
                'facebook'        => 'liderdosvidros',
                'endereco'        => 'Goiânia e região',
                'areas_atendidas' => ['Goiânia', 'Aparecida de Goiânia', 'Senador Canedo', 'Anápolis', 'Goianira'],
                'google_ads_id'         => 'AW-666035862',
                'google_maps_embed'     => null,
                'google_drive_api_key'  => 'AIzaSyA1BlRpL0Hq-l_4Ifc2HHyzteUp9QmN4Mc',
                'google_drive_folder_id'=> '1qYHQqfFC0F8sdQzv3xVxlBax8ZzLR2rK',
                'schema_type'     => 'LocalBusiness',
                'seo_title'       => 'Vidraçaria em Goiânia | Box Banheiro, Espelhos Sob Medida, Esquadrias e Portas de Vidro',
                'seo_description' => 'Vidraçaria em Goiânia especializada em box banheiro Goiânia, espelhos sob medida, portas de vidro, esquadrias de alumínio e fechamento de sacada Goiânia. Atendimento em Aparecida de Goiânia, Senador Canedo, Anápolis e Goianira.',
                'seo_keywords'    => 'vidraçaria em goiânia, box banheiro goiânia, espelhos sob medida, esquadrias de alumínio, portas de vidro, fechamento de sacada goiania, vidraçaria em senador canedo, vidraçaria em aparecida de goiania, vidraçaria em anapolis, vidraçaria em goianira',
                'hero_titulo'     => 'Vidraçaria em Goiânia',
                'hero_subtitulo'  => 'Box banheiro, espelhos e esquadrias de alumínio',
                'sobre_titulo'    => 'Líder Vidros',
                'sobre_descricao' => 'Atuando há mais de 7 anos no mercado, a Líder Vidros continua buscando ser referência no segmento de VIDRAÇARIA em Goiânia e região. Contamos com uma equipe qualificada, eficiente e comprometida para superar as expectativas dos nossos clientes. Seriedade, transparência e, sobretudo, respeito compõem nossos valores.',
                'sobre_missao'    => 'Oferecer atendimento e produtos de qualidade aos nossos clientes.',
                'sobre_visao'     => 'Ser referência no segmento de VIDRAÇARIA em Goiânia e região.',
                'sobre_valores'   => 'Seriedade, transparência e, sobretudo, respeito.',
                'logo'            => $this->copyAsset("{$source}/images/logo.png", 'tenants/logos/lider-vidros.png'),
                'favicon'         => $this->copyAsset("{$source}/images/favicon.ico", 'tenants/favicons/lider-vidros.ico'),
                'og_image'        => $this->copyAsset("{$source}/images/thumbnail-preview-link-whatsapp.jpeg", 'tenants/og-images/lider-vidros.jpeg'),
            ]
        );

        // Serviços
        $this->seedServices($tenant, [
            ['slug' => 'vidros',   'titulo' => 'Espelhos e Vidraçaria',  'descricao' => 'Espelhos sob medida, vidros temperados, box banheiro e portas de vidro com instalação profissional.',          'ordem' => 0],
            ['slug' => 'aluminio', 'titulo' => 'Esquadrias e Alumínio',  'descricao' => 'Janelas, portas, fachadas e esquadrias de alumínio com vidro temperado. Projetos personalizados.',              'ordem' => 1],
            ['slug' => 'cortina',  'titulo' => 'Cortinas e Películas',   'descricao' => 'Películas de controle solar, proteção UV, privacidade e cortinas para residências e empresas.',                  'ordem' => 2],
        ]);

        // Slides
        $this->seedSlides($tenant, [
            ['src' => "{$source}/images/ba1.jpg", 'dest' => 'tenants/slides/lider-vidros/slide-1.jpg', 'legenda' => null,                             'ordem' => 0],
            ['src' => "{$source}/images/ba2.jpg", 'dest' => 'tenants/slides/lider-vidros/slide-2.jpg', 'legenda' => null,                             'ordem' => 1],
            ['src' => "{$source}/images/ba3.jpg", 'dest' => 'tenants/slides/lider-vidros/slide-3.jpg', 'legenda' => 'Líder Vidros — Goiânia e região', 'ordem' => 2],
        ]);

        // Features da home
        $f1 = $this->copyMultiple("{$source}/images", ['f1.jpg', 'f2.jpg', 'f3.jpg'], 'tenants/features/lider-vidros/peliculas');
        $f2 = $this->copyMultiple("{$source}/images", ['f4.jpg'],                      'tenants/features/lider-vidros/guarda-corpo');

        $this->seedFeatures($tenant, [
            ['titulo' => 'Películas', 'descricao' => 'Redução de calor, proteção contra raios UV, controle de luminosidade, economia de energia, segurança e sofisticação.', 'tipo' => 'servico', 'rota' => 'cortina',  'imagens' => $f1, 'ordem' => 0],
            ['titulo' => 'Guarda-corpo', 'descricao' => 'São colocados em escadas, bordas de sacadas, rampas, passarelas e mezaninos.',                                        'tipo' => 'servico', 'rota' => 'aluminio', 'imagens' => $f2, 'ordem' => 1],
        ]);

        // Galeria — Vidros (g1–g7)
        $this->seedGallerySet($tenant, 'vidros', "{$source}/images/servicos", [
            ['file' => 'g1.jpg', 'titulo' => 'Espelhos'],
            ['file' => 'g2.jpg', 'titulo' => 'Janelas'],
            ['file' => 'g3.jpg', 'titulo' => 'Portas de Vidro'],
            ['file' => 'g4.jpg', 'titulo' => 'Cortinas'],
            ['file' => 'g5.jpg', 'titulo' => 'Box p/ Banheiro'],
            ['file' => 'g6.jpg', 'titulo' => 'Películas'],
            ['file' => 'g7.jpg', 'titulo' => 'Guarda-corpo'],
        ], 'gallery/lider-vidros/vidros');

        // Galeria — Alumínio (g1–g4)
        $this->seedGallerySet($tenant, 'aluminio', "{$source}/images/servicos/aluminio", [
            ['file' => 'g1.jpg', 'titulo' => 'Esquadrias em Alumínio'],
            ['file' => 'g2.jpg', 'titulo' => 'Janelas em Alumínio'],
            ['file' => 'g3.jpg', 'titulo' => 'Fachadas Pele de Vidro'],
            ['file' => 'g4.jpg', 'titulo' => 'Manutenção de Esquadrias'],
        ], 'gallery/lider-vidros/aluminio');

        // Galeria — Catálogo geral (126 fotos)
        $this->seedGaleriaCatalogo($tenant, "{$source}/images/catalogo", 'gallery/lider-vidros/catalogo', 'geral');

        $this->command->info("  ✓ Líder Vidros importado.");
    }

    // ─────────────────────────────────────────────────
    //  BOX E VIDROS GOIÂNIA
    // ─────────────────────────────────────────────────

    private function seedBoxVidros(): void
    {
        $this->command->info('▶ Box e Vidros Goiânia...');

        $source = "{$this->taprootProjects}/box-vidros";

        $tenant = Tenant::updateOrCreate(
            ['slug' => 'box-vidros'],
            [
                'nome'            => 'Box e Vidros Goiânia',
                'dominio'         => 'boxevidros.com.br',
                'ativo'           => true,
                'cor_primaria'    => '#183b9f',
                'cor_secundaria'  => '#1a2a6c',
                'whatsapp'        => '5562981627155',
                'whatsapp_exibir' => '(62) 9 8162-7155',
                'email'           => 'contato@boxevidros.com.br',
                'instagram'       => 'boxevidrosgyn',
                'facebook'        => 'boxevidrosgyn',
                'endereco'        => 'Goiânia e região',
                'areas_atendidas' => ['Goiânia', 'Aparecida de Goiânia', 'Senador Canedo', 'Anápolis', 'Goianira'],
                'google_ads_id'   => null,
                'google_maps_embed' => null,
                'schema_type'     => 'LocalBusiness',
                'seo_title'       => 'Vidraçaria em Goiânia | Box Banheiro, Espelhos Sob Medida, Esquadrias e Portas de Vidro',
                'seo_description' => 'Vidraçaria em Goiânia especializada em box banheiro Goiânia, espelhos sob medida, portas de vidro, esquadrias de alumínio e fechamento de sacada Goiânia. Atendimento em Aparecida de Goiânia, Senador Canedo, Anápolis e Goianira.',
                'seo_keywords'    => 'vidraçaria em goiânia, box banheiro goiânia, espelhos sob medida, esquadrias de alumínio, portas de vidro, fechamento de sacada goiania, vidraçaria em senador canedo, vidraçaria em aparecida de goiania, vidraçaria em anapolis, vidraçaria em goianira',
                'hero_titulo'     => 'Vidraçaria em Goiânia',
                'hero_subtitulo'  => 'Box banheiro, espelhos e esquadrias de alumínio',
                'sobre_titulo'    => 'Box e Vidros Goiânia',
                'sobre_descricao' => 'Atuando há mais de 7 anos no mercado, a Box e Vidros Goiânia continua buscando ser referência no segmento de VIDRAÇARIA em Goiânia e região. Contamos com uma equipe qualificada, eficiente e comprometida para superar as expectativas dos nossos clientes. Seriedade, transparência e, sobretudo, respeito compõem nossos valores.',
                'sobre_missao'    => 'Oferecer atendimento e produtos de qualidade aos nossos clientes.',
                'sobre_visao'     => 'Ser referência no segmento de VIDRAÇARIA em Goiânia e região.',
                'sobre_valores'   => 'Seriedade, transparência e, sobretudo, respeito.',
                'logo'            => $this->copyAsset("{$source}/images/logo.png",                            'tenants/logos/box-vidros.png'),
                'favicon'         => $this->copyAsset("{$source}/images/favicon.png",                         'tenants/favicons/box-vidros.png'),
                'og_image'        => $this->copyAsset("{$source}/images/thumbnail-preview-link-whatsapp.jpeg", 'tenants/og-images/box-vidros.jpeg'),
            ]
        );

        // Serviços
        $this->seedServices($tenant, [
            ['slug' => 'vidros',   'titulo' => 'Espelhos e Vidraçaria',  'descricao' => 'Espelhos sob medida, vidros temperados e decorativos com instalação profissional e acabamento perfeito para sua casa ou empresa.',  'ordem' => 0],
            ['slug' => 'aluminio', 'titulo' => 'Esquadrias de Alumínio', 'descricao' => 'Janelas, portas e esquadrias de alumínio com vidro temperado. Projetos personalizados e instalação completa.',                       'ordem' => 1],
            ['slug' => 'cortina',  'titulo' => 'Cortinas e Acessórios',  'descricao' => 'Cortinas, películas e acessórios para controle de luminosidade e privacidade.',                                                        'ordem' => 2],
        ]);

        // Slides (4 slides dedicados)
        $this->seedSlides($tenant, [
            ['src' => "{$source}/images/slide/slide1.jpg", 'dest' => 'tenants/slides/box-vidros/slide-1.jpg', 'legenda' => null,                                    'ordem' => 0],
            ['src' => "{$source}/images/slide/slide2.jpg", 'dest' => 'tenants/slides/box-vidros/slide-2.jpg', 'legenda' => null,                                    'ordem' => 1],
            ['src' => "{$source}/images/slide/slide3.jpg", 'dest' => 'tenants/slides/box-vidros/slide-3.jpg', 'legenda' => null,                                    'ordem' => 2],
            ['src' => "{$source}/images/slide/slide4.jpg", 'dest' => 'tenants/slides/box-vidros/slide-4.jpg', 'legenda' => 'Box e Vidros — Goiânia e região',       'ordem' => 3],
        ]);

        // Features da home
        $f1 = $this->copyMultiple("{$source}/images/features", ['f1.jpg', 'f2.jpg', 'f3.jpg'], 'tenants/features/box-vidros/espelhos');
        $f2 = $this->copyMultiple("{$source}/images/features", ['f4.jpg'],                      'tenants/features/box-vidros/aluminio');

        $this->seedFeatures($tenant, [
            ['titulo' => 'Espelhos e Vidros',       'descricao' => 'Espelhos sob medida, vidros temperados e decorativos com instalação profissional e acabamento perfeito para sua casa ou empresa.',          'tipo' => 'servico', 'rota' => 'vidros',   'imagens' => $f1, 'ordem' => 0],
            ['titulo' => 'Esquadrias de Alumínio',  'descricao' => 'Janelas, portas e esquadrias de alumínio com vidro temperado para residências e empresas. Projetos personalizados e instalação completa.', 'tipo' => 'servico', 'rota' => 'aluminio', 'imagens' => $f2, 'ordem' => 1],
        ]);

        // Galeria — Vidros (g1–g7)
        $this->seedGallerySet($tenant, 'vidros', "{$source}/images/servicos", [
            ['file' => 'g1.jpg', 'titulo' => 'Espelhos'],
            ['file' => 'g2.jpg', 'titulo' => 'Janelas'],
            ['file' => 'g3.jpg', 'titulo' => 'Portas de Vidro'],
            ['file' => 'g4.jpg', 'titulo' => 'Cortinas'],
            ['file' => 'g5.jpg', 'titulo' => 'Box p/ Banheiro'],
            ['file' => 'g6.jpg', 'titulo' => 'Películas'],
            ['file' => 'g7.jpg', 'titulo' => 'Guarda-corpo'],
        ], 'gallery/box-vidros/vidros');

        // Galeria — Alumínio (g1–g4)
        $this->seedGallerySet($tenant, 'aluminio', "{$source}/images/servicos/aluminio", [
            ['file' => 'g1.jpg', 'titulo' => 'Esquadrias em Alumínio'],
            ['file' => 'g2.jpg', 'titulo' => 'Janelas em Alumínio'],
            ['file' => 'g3.jpg', 'titulo' => 'Fachadas Pele de Vidro'],
            ['file' => 'g4.jpg', 'titulo' => 'Manutenção de Esquadrias'],
        ], 'gallery/box-vidros/aluminio');

        // Galeria — Catálogo geral (11 fotos próprias)
        $this->seedGaleriaCatalogo($tenant, "{$source}/images/catalogo", 'gallery/box-vidros/catalogo', 'geral');

        $this->command->info("  ✓ Box e Vidros Goiânia importado.");
    }

    // ─────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────

    private function seedServices(Tenant $tenant, array $services): void
    {
        foreach ($services as $data) {
            TenantService::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $data['slug']],
                ['titulo' => $data['titulo'], 'descricao' => $data['descricao'], 'ativo' => true, 'ordem' => $data['ordem']]
            );
        }
    }

    private function seedSlides(Tenant $tenant, array $slides): void
    {
        // Limpa slides existentes deste tenant para re-seed limpo
        if ($tenant->wasRecentlyCreated) {
            TenantSlide::where('tenant_id', $tenant->id)->delete();
        }

        foreach ($slides as $slide) {
            $path = $this->copyAsset($slide['src'], $slide['dest']);
            if (!$path) {
                $this->command->warn("  ⚠ Slide não encontrado: {$slide['src']}");
                continue;
            }
            TenantSlide::updateOrCreate(
                ['tenant_id' => $tenant->id, 'path' => $path],
                ['legenda' => $slide['legenda'], 'ordem' => $slide['ordem'], 'ativo' => true]
            );
        }
    }

    private function seedFeatures(Tenant $tenant, array $features): void
    {
        foreach ($features as $feat) {
            TenantFeature::updateOrCreate(
                ['tenant_id' => $tenant->id, 'titulo' => $feat['titulo']],
                [
                    'descricao' => $feat['descricao'],
                    'tipo'      => $feat['tipo'],
                    'rota'      => $feat['rota'],
                    'imagens'   => array_filter($feat['imagens']),
                    'ativo'     => true,
                    'ordem'     => $feat['ordem'],
                ]
            );
        }
    }

    private function seedGallerySet(Tenant $tenant, string $categoria, string $sourceDir, array $items, string $destDir): void
    {
        foreach ($items as $ordem => $item) {
            $src  = "{$sourceDir}/{$item['file']}";
            $dest = "{$destDir}/{$item['file']}";
            $path = $this->copyAsset($src, $dest);

            if (!$path) {
                $this->command->warn("  ⚠ Imagem não encontrada: {$src}");
                continue;
            }

            GalleryImage::updateOrCreate(
                ['tenant_id' => $tenant->id, 'categoria' => $categoria, 'path' => $path],
                ['tenant_slug' => $tenant->slug, 'titulo' => $item['titulo'], 'ativo' => true, 'ordem' => $ordem]
            );
        }
    }

    private function seedGaleriaCatalogo(Tenant $tenant, string $sourceDir, string $destDir, string $categoria): void
    {
        if (!is_dir($sourceDir)) {
            $this->command->warn("  ⚠ Diretório de catálogo não encontrado: {$sourceDir}");
            return;
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG'];
        $files = [];

        foreach ($extensions as $ext) {
            $found = glob("{$sourceDir}/*.{$ext}");
            if ($found) {
                $files = array_merge($files, $found);
            }
        }

        if (empty($files)) {
            $this->command->warn("  ⚠ Nenhuma imagem no catálogo: {$sourceDir}");
            return;
        }

        sort($files);
        $count = 0;

        foreach ($files as $ordem => $srcFile) {
            $filename = strtolower(basename($srcFile));
            $ext      = strtolower(pathinfo($srcFile, PATHINFO_EXTENSION));
            $slug     = pathinfo($filename, PATHINFO_FILENAME);
            $dest     = "{$destDir}/{$slug}.{$ext}";
            $path     = $this->copyAsset($srcFile, $dest);

            if (!$path) {
                continue;
            }

            GalleryImage::updateOrCreate(
                ['tenant_id' => $tenant->id, 'categoria' => $categoria, 'path' => $path],
                ['tenant_slug' => $tenant->slug, 'titulo' => null, 'ativo' => true, 'ordem' => $ordem]
            );
            $count++;
        }

        $this->command->info("  → {$count} imagens do catálogo importadas.");
    }

    /**
     * Copia um único asset para o storage e retorna o path relativo.
     * Retorna null se a origem não existir.
     */
    private function copyAsset(string $srcAbsolute, string $destRelative): ?string
    {
        if (!file_exists($srcAbsolute)) {
            return null;
        }

        $dir = dirname($destRelative);
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $destAbsolute = storage_path("app/public/{$destRelative}");

        if (!file_exists($destAbsolute)) {
            if (!copy($srcAbsolute, $destAbsolute)) {
                $this->command->warn("  ⚠ Falha ao copiar: {$srcAbsolute}");
                return null;
            }
        }

        return $destRelative;
    }

    /**
     * Copia múltiplos assets de um diretório fonte e retorna array de paths.
     * Arquivos inexistentes são silenciosamente ignorados.
     */
    private function copyMultiple(string $srcDir, array $files, string $destDirRelative): array
    {
        $paths = [];
        foreach ($files as $i => $file) {
            $path = $this->copyAsset("{$srcDir}/{$file}", "{$destDirRelative}-" . ($i + 1) . '.' . pathinfo($file, PATHINFO_EXTENSION));
            if ($path) {
                $paths[] = $path;
            }
        }
        return $paths;
    }
}
