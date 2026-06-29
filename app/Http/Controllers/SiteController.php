<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function sobre()
    {
        return view('sobre');
    }

    public function catalogo()
    {
        $t = config('tenant');
        $images = \App\Models\GalleryImage::where('tenant_slug', $t['slug'])
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
            fn ($m) => $m->to($t['email'])->subject("Contato via site — {$t['nome']}")
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

        $images = \App\Models\GalleryImage::where('tenant_slug', $t['slug'])
            ->where('categoria', $slug)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        return view('servico', compact('servico', 'images'));
    }
}
