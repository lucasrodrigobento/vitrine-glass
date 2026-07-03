@extends('layouts.app')
@php
    $t    = config('tenant');
    $heroH = $t['paginas']['home']['titulo_hero']    ?? '';
    $heroS = $t['paginas']['home']['subtitulo_hero'] ?? '';
@endphp

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endsection

@section('content')

{{-- Hero Slider --}}
@if($slides->isNotEmpty())
<section class="hero-swiper" aria-label="Destaques {{ $t['nome'] }}">
    <div class="swiper" id="heroSwiper">
        <div class="swiper-wrapper">
            @foreach($slides as $slide)
            @php
                $slideH   = $slide->titulo      ?: $heroH;
                $slideS   = $slide->subtitulo   ?: $heroS;
                $slideBtn = $slide->botao_label  ?: '';
                $slideBtnUrl = $slide->botao_url ?: '';
            @endphp
            <div class="swiper-slide">
                <img src="{{ Storage::url($slide->path) }}"
                     alt="{{ $slide->legenda ?: $t['nome'] }}"
                     class="hero-swiper__img"
                     loading="{{ $loop->first ? 'eager' : 'lazy' }}">

                <div class="hero-swiper__overlay" aria-hidden="true"></div>

                @if($slideH)
                <div class="hero-swiper__content">
                    <h2>{{ $slideH }}</h2>
                    @if($slideS)<p>{{ $slideS }}</p>@endif
                    @if($slideBtn && $slideBtnUrl)
                    <a href="{{ $slideBtnUrl }}" class="hero-swiper__cta">{{ $slideBtn }}</a>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if($slides->count() > 1)
        <div class="swiper-button-prev" role="button" aria-label="Slide anterior"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
        <div class="swiper-button-next" role="button" aria-label="Próximo slide"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
        <div class="swiper-pagination"></div>
        @endif
    </div>
</section>
@endif

{{-- Features --}}
@if(!empty($t['features']))
<div class="features-w3">
    <div class="container">
        <div class="features-grids row">
            @foreach($t['features'] as $feat)
            @php
                $link = $feat['tipo'] === 'servico'
                    ? route('servico', $feat['rota'])
                    : route($feat['rota']);
                $imgs = $feat['imagens'] ?? [];
            @endphp
            @if(!empty($imgs))
            <div class="col-md-6 feat-grid">
                @if(count($imgs) > 1)
                    <div class="feat-top">
                        <div class="feat-left">
                            <div class="multi-gd-text">
                                <a href="{{ $link }}">
                                    <img src="{{ Storage::url($imgs[0]) }}" alt="{{ $feat['titulo'] }}">
                                </a>
                            </div>
                        </div>
                        <div class="feat-right">
                            @foreach(array_slice($imgs, 1) as $img)
                            <div class="multi-gd-text">
                                <a href="{{ $link }}">
                                    <img src="{{ Storage::url($img) }}" alt="{{ $feat['titulo'] }}" loading="lazy">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="multi-gd-text">
                        <a href="{{ $link }}">
                            <img src="{{ Storage::url($imgs[0]) }}" alt="{{ $feat['titulo'] }}">
                        </a>
                    </div>
                @endif
                <h4>{{ $feat['titulo'] }}</h4>
                <p>{{ $feat['descricao'] }}</p>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
(function () {
    var el = document.getElementById('heroSwiper');
    if (!el) return;
    new Swiper(el, {
        effect: 'fade',
        fadeEffect: { crossFade: true },
        loop: {{ $slides->count() > 1 ? 'true' : 'false' }},
        speed: 900,
        @if($slides->count() > 1)
        autoplay: {
            delay: 4500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        navigation: {
            prevEl: '#heroSwiper .swiper-button-prev',
            nextEl: '#heroSwiper .swiper-button-next',
        },
        pagination: {
            el: '#heroSwiper .swiper-pagination',
            clickable: true,
        },
        @endif
        keyboard: { enabled: true },
        a11y: {
            prevSlideMessage: 'Slide anterior',
            nextSlideMessage: 'Próximo slide',
        },
    });
})();
</script>
@endsection
