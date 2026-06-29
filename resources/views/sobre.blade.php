@extends('layouts.app')
@php $t = config('tenant'); $p = $t['paginas']['sobre']; @endphp

@section('title', $p['titulo'] . ' — ' . $t['nome'])

@section('content')

<div class="page-banner banner-empresa">
    <div class="container">
        <h1>{{ $p['titulo'] }}</h1>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <p class="lead">{{ $p['descricao'] }}</p>

                <div class="row mt-5 g-4">
                    <div class="col-md-4">
                        <div class="mvv-card text-center p-4">
                            <i class="fa fa-bullseye fa-2x mb-3"></i>
                            <h4>Missão</h4>
                            <p>{{ $p['missao'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mvv-card text-center p-4">
                            <i class="fa fa-eye fa-2x mb-3"></i>
                            <h4>Visão</h4>
                            <p>{{ $p['visao'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mvv-card text-center p-4">
                            <i class="fa fa-heart fa-2x mb-3"></i>
                            <h4>Valores</h4>
                            <p>{{ $p['valores'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
