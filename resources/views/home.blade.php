@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('content')

{{-- Slider --}}
@if($slides->isNotEmpty())
<div class="demo-2">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($slides as $i => $slide)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ Storage::url($slide->path) }}"
                     class="d-block w-100"
                     alt="{{ $slide->legenda ?: $t['nome'] }}"
                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                @if($slide->legenda)
                <div class="carousel-caption d-none d-md-block">
                    <p>{{ $slide->legenda }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($slides->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        @endif
    </div>
</div>
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
