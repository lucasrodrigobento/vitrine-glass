@extends('layouts.app')
@php $t = config('tenant'); $p = $t['paginas']['sobre']; @endphp

@section('title', $p['titulo'] . ' — ' . $t['nome'])

@section('content')

<div class="banner1 banner-empresa">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>{{ $p['titulo'] }}</span>
        </h3>
    </div>
</div>

<div class="about-w3">
    <div class="container">
        <h2 class="tittle">{{ $p['titulo'] }}</h2>
        <div class="about-grids row">
            <div class="col-md-8 about-grid1">
                <p>{{ $p['descricao'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="whychoose-w3ls">
    <div class="container">
        <h2 class="tittle">Nossa Filosofia</h2>
        <div class="choose-grids row">
            <div class="col-md-4 choose-grid">
                <i class="fa fa-bullseye"></i>
                <h4>Missão</h4>
                <p>{{ $p['missao'] }}</p>
            </div>
            <div class="col-md-4 choose-grid">
                <i class="fa fa-eye"></i>
                <h4>Visão</h4>
                <p>{{ $p['visao'] }}</p>
            </div>
            <div class="col-md-4 choose-grid">
                <i class="fa fa-heart"></i>
                <h4>Valores</h4>
                <p>{{ $p['valores'] }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
