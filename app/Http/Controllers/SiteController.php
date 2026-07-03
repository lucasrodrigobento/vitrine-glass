<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Tenant;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SiteController extends Controller
{
    private function tenant(): Tenant
    {
        return app('tenant');
    }

    public function home()
    {
        $slides = $this->tenant()
            ->slides()
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        return view('home', compact('slides'));
    }

    public function sobre()
    {
        return view('sobre');
    }

    public function catalogo(Request $request, GoogleDriveService $drive)
    {
        $tenant = $this->tenant();

        if ($tenant->google_drive_api_key && $tenant->google_drive_folder_id) {
            $allFiles = $drive->catalogImages(
                $tenant->slug,
                $tenant->google_drive_api_key,
                $tenant->google_drive_folder_id
            );

            $perPage = 10;
            $page    = max(1, (int) $request->get('page', 1));
            $total   = count($allFiles);
            $items   = array_slice($allFiles, ($page - 1) * $perPage, $perPage);

            $images = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => route('catalogo')]
            );

            return view('catalogo', ['images' => $images, 'mode' => 'drive', 'drive' => $drive]);
        }

        $images = GalleryImage::where('tenant_id', $tenant->id)
            ->where('categoria', 'geral')
            ->where('ativo', true)
            ->orderBy('ordem')
            ->paginate(10);

        return view('catalogo', ['images' => $images, 'mode' => 'local']);
    }

    public function contato()
    {
        return view('contato');
    }

    public function enviarContato(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:100',
            'email'    => 'required|email',
            'mensagem' => 'required|string|max:2000',
        ]);

        $t = config('tenant');

        \Illuminate\Support\Facades\Mail::raw(
            "Nome: {$request->nome}\nEmail: {$request->email}\n\n{$request->mensagem}",
            fn($m) => $m->to($t['email'])->subject("Contato via site — {$t['nome']}")
        );

        return back()->with('sucesso', 'Mensagem enviada com sucesso!');
    }

    public function sitemap()
    {
        $t       = config('tenant');
        $base    = rtrim(url('/'), '/');
        $today   = now()->toDateString();
        $servicos = collect($t['servicos'] ?? []);

        $urls = [
            ['loc' => $base . '/',         'changefreq' => 'weekly',  'priority' => '1.0'],
            ['loc' => $base . '/empresa',  'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $base . '/catalogo', 'changefreq' => 'weekly',  'priority' => '0.7'],
            ['loc' => $base . '/contato',  'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        foreach ($servicos as $s) {
            if ($s['ativo']) {
                $urls[] = [
                    'loc'        => $base . '/servicos/' . $s['slug'],
                    'changefreq' => 'monthly',
                    'priority'   => '0.9',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n"
                . '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n"
                . "    <lastmod>{$today}</lastmod>\n"
                . "    <changefreq>{$u['changefreq']}</changefreq>\n"
                . "    <priority>{$u['priority']}</priority>\n"
                . "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function servico(string $slug)
    {
        $t        = config('tenant');
        $servicos = collect($t['servicos'] ?? []);
        $servico  = $servicos->firstWhere('slug', $slug);

        if (!$servico || !$servico['ativo']) {
            abort(404);
        }

        $images = GalleryImage::where('tenant_id', $this->tenant()->id)
            ->where('categoria', $slug)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        return view('servico', compact('servico', 'images'));
    }
}
