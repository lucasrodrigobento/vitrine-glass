<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Tenant;
use Illuminate\Http\Request;

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

    public function catalogo()
    {
        $images = GalleryImage::where('tenant_id', $this->tenant()->id)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->paginate(12);

        return view('catalogo', compact('images'));
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
