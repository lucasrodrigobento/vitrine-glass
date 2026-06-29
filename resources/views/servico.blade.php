@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', $servico['titulo'] . ' — ' . $t['nome'])

@section('content')

<div class="page-banner banner-servicos">
    <div class="container">
        <h1>{{ $servico['titulo'] }}</h1>
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
                         class="img-fluid rounded" alt="{{ $img->titulo ?: $servico['titulo'] }}">
                    @if($img->titulo)
                    <div class="gallery-overlay">{{ $img->titulo }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted py-5">Conteúdo em breve.</p>
        @endif

        <div class="text-center mt-5">
            <a href="{{ route('contato') }}" class="btn btn-primary btn-lg">
                Solicitar Orçamento
            </a>
        </div>
    </div>
</section>

@endsection
