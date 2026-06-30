@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', 'Catálogo — ' . $t['nome'])

@section('content')

<div class="banner1 banner-servicos">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>Serviços</span>
        </h3>
    </div>
</div>

<div class="gallery-w3l">
    <div class="container">
        <h2 class="tittle">Galeria de Serviços</h2>
        <div class="gallery-grids row">
            @if($images->count())
                @foreach($images as $img)
                <div class="col-md-6 col-sm-6 gallery-grids-left">
                    <div class="gallery-grid">
                        <img src="{{ asset('storage/' . $img->path) }}"
                             class="img-fluid"
                             alt="{{ $img->titulo ?: $t['nome'] }}"
                             loading="lazy">
                        @if($img->titulo)
                        <p class="text-center mt-1">{{ $img->titulo }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <p>Nenhuma imagem cadastrada ainda.</p>
                </div>
            @endif
        </div>
        @if($images->count())
        <div class="text-center mt-4">{{ $images->links() }}</div>
        @endif
    </div>
</div>

@endsection
