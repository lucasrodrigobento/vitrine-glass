@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('content')

{{-- Hero slider --}}
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach(range(1, $t['slides_count'] ?? 4) as $i)
            <div class="carousel-item {{ $i === 1 ? 'active' : '' }}">
                <img src="{{ asset("tenants/{$t['slug']}/slides/slide{$i}.jpg") }}"
                     class="d-block w-100" alt="{{ $t['nome'] }} — slide {{ $i }}">
                @if($i === 1)
                <div class="carousel-caption">
                    <h1>{{ $t['paginas']['home']['titulo_hero'] }}</h1>
                    <p>{{ $t['paginas']['home']['subtitulo_hero'] }}</p>
                    <a href="{{ route('contato') }}" class="btn btn-primary btn-lg mt-2">Solicitar Orçamento</a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

{{-- Features — layout assimétrico idêntico ao original --}}
@if(!empty($t['features']))
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            @foreach($t['features'] as $feat)
            @php
                $link = $feat['tipo'] === 'servico'
                    ? route('servico', $feat['rota'])
                    : route($feat['rota']);
                $imgs = $feat['imagens'];
            @endphp
            <div class="feat-group">
                @if(count($imgs) === 1)
                    <a href="{{ $link }}" class="feat-img-single">
                        <img src="{{ asset("tenants/{$t['slug']}/features/{$imgs[0]}") }}"
                             alt="{{ $feat['titulo'] }}">
                    </a>
                @else
                    <div class="feat-img-multi">
                        <div class="feat-img-large">
                            <a href="{{ $link }}">
                                <img src="{{ asset("tenants/{$t['slug']}/features/{$imgs[0]}") }}"
                                     alt="{{ $feat['titulo'] }}">
                            </a>
                        </div>
                        <div class="feat-img-stack">
                            @foreach(array_slice($imgs, 1) as $img)
                            <img src="{{ asset("tenants/{$t['slug']}/features/{$img}") }}"
                                 alt="{{ $feat['titulo'] }}">
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="feat-text">
                    <h4><a href="{{ $link }}">{{ $feat['titulo'] }}</a></h4>
                    <p>{{ $feat['descricao'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="cta-section py-5 text-center text-white">
    <div class="container">
        <h2>Solicite um Orçamento Gratuito</h2>
        <p class="lead">Entre em contato e receba atendimento personalizado</p>
        <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}"
           class="btn btn-light btn-lg me-2" target="_blank" rel="noopener">
            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
        </a>
        <a href="{{ route('contato') }}" class="btn btn-outline-light btn-lg">Formulário de Contato</a>
    </div>
</section>

@endsection
