@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', $servico['titulo'] . ' — ' . $t['nome'])

@section('content')

<div class="banner1 banner-servicos">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>{{ $servico['titulo'] }}</span>
        </h3>
    </div>
</div>

<div class="gallery-w3l">
    <div class="container">
        <h2 class="tittle">{{ $servico['titulo'] }}</h2>
        <div class="gallery-grids row">
            @if($images->count())
                @foreach($images as $img)
                <div class="col-md-6 col-sm-6 gallery-grids-left">
                    <div class="gallery-grid">
                        <img src="{{ asset('storage/' . $img->path) }}"
                             class="img-fluid"
                             alt="{{ $img->titulo ?: $servico['titulo'] }}"
                             loading="lazy">
                        @if($img->titulo)
                        <p class="text-center mt-1">{{ $img->titulo }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <p>Conteúdo em breve.</p>
                </div>
            @endif
        </div>

        <div class="text-center mt-5">
            <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}"
               class="btn btn-primary btn-lg me-2" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
            </a>
            <a href="{{ route('contato') }}" class="btn btn-outline-primary btn-lg">
                Formulário de Contato
            </a>
        </div>
    </div>
</div>

@endsection
