@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', 'Catálogo — ' . $t['nome'])

@section('content')

<div class="page-banner banner-servicos">
    <div class="container">
        <h1>Galeria de Serviços</h1>
    </div>
</div>

<section class="py-5">
    <div class="container">
        @if($images->count())
        <div class="row g-3">
            @foreach($images as $img)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="gallery-item">
                    <img src="{{ asset('storage/' . $img->path) }}"
                         class="img-fluid rounded" alt="{{ $img->titulo ?: $t['nome'] }}">
                    @if($img->titulo)
                    <div class="gallery-overlay">{{ $img->titulo }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $images->links() }}</div>
        @else
        <p class="text-center text-muted py-5">Nenhuma imagem cadastrada ainda.</p>
        @endif
    </div>
</section>

@endsection
