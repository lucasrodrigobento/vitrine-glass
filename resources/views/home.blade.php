@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('content')

{{-- Slider --}}
<div class="demo-2">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach(range(1, $t['slides_count'] ?? 4) as $i)
            <div class="carousel-item {{ $i === 1 ? 'active' : '' }}">
                <img src="{{ asset("tenants/{$t['slug']}/slides/slide{$i}.jpg") }}"
                     class="d-block w-100" alt="{{ $t['nome'] }} — slide {{ $i }}">
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
</div>

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
                $imgs = $feat['imagens'];
                $slug = $t['slug'];
            @endphp
            <div class="col-md-6 feat-grid">
                @if(count($imgs) > 1)
                    <div class="feat-top">
                        <div class="feat-left">
                            <div class="multi-gd-text">
                                <a href="{{ $link }}">
                                    <img src="{{ asset("tenants/{$slug}/features/{$imgs[0]}") }}"
                                         alt="{{ $feat['titulo'] }}">
                                </a>
                            </div>
                        </div>
                        <div class="feat-right">
                            @foreach(array_slice($imgs, 1) as $img)
                            <div class="multi-gd-text">
                                <a href="{{ $link }}">
                                    <img src="{{ asset("tenants/{$slug}/features/{$img}") }}"
                                         alt="{{ $feat['titulo'] }}">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="multi-gd-text">
                        <a href="{{ $link }}">
                            <img src="{{ asset("tenants/{$slug}/features/{$imgs[0]}") }}"
                                 alt="{{ $feat['titulo'] }}">
                        </a>
                    </div>
                @endif
                <h4>{{ $feat['titulo'] }}</h4>
                <p>{{ $feat['descricao'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
