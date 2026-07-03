@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', 'Contato — ' . $t['nome'])

@section('content')

<div class="banner1 banner-contato">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>Contato</span>
        </h3>
    </div>
</div>

<div class="mail-w3">
    <div class="container">
        <h2 class="tittle">Entre em Contato</h2>
        <div class="mail-w3l row">

            <div class="col-md-6 mail-grid">
                @if(session('sucesso'))
                <div class="alert alert-success mb-3">{{ session('sucesso') }}</div>
                @endif

                <form method="POST" action="{{ route('contato.enviar') }}">
                    @csrf
                    <input type="text" name="nome" placeholder="Seu Nome"
                           value="{{ old('nome') }}" required>
                    @error('nome')<div class="alert-danger p-1 mb-1">{{ $message }}</div>@enderror

                    <input type="email" name="email" placeholder="Seu E-mail"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="alert-danger p-1 mb-1">{{ $message }}</div>@enderror

                    <textarea name="mensagem" placeholder="Sua Mensagem" required>{{ old('mensagem') }}</textarea>
                    @error('mensagem')<div class="alert-danger p-1 mb-1">{{ $message }}</div>@enderror

                    <input type="submit" value="Enviar Mensagem">
                </form>
            </div>

            <div class="col-md-3 mail-grid">
                <h4>WhatsApp / Redes</h4>
                <p>
                    <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp me-2"></i>{{ $t['whatsapp_exibir'] }}
                    </a>
                </p>
                @if(!empty($t['instagram']))
                <p>
                    <a href="https://www.instagram.com/{{ $t['instagram'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-instagram me-2"></i>{{ '@' . $t['instagram'] }}
                    </a>
                </p>
                @endif
                @if(!empty($t['facebook']))
                <p>
                    <a href="https://www.facebook.com/{{ $t['facebook'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-facebook me-2"></i>/{{ $t['facebook'] }}
                    </a>
                </p>
                @endif
            </div>

            <div class="col-md-3 mail-grid">
                <h4>Endereço</h4>
                <p><i class="fa fa-map-marker-alt me-2"></i>{{ $t['endereco'] }}</p>
                <p>
                    <a href="mailto:{{ $t['email'] }}">
                        <i class="fa fa-envelope me-2"></i>{{ $t['email'] }}
                    </a>
                </p>
            </div>

        </div>

        @php
            $mapSrc = null;
            if (!empty($t['google_maps_query'])) {
                $mapSrc = 'https://maps.google.com/maps?q=' . urlencode($t['google_maps_query']) . '&output=embed&hl=pt-BR';
            } elseif (!empty($t['google_maps_embed'])) {
                $mapSrc = $t['google_maps_embed'];
            }
        @endphp
        @if($mapSrc)
        <div class="map">
            <iframe src="{{ $mapSrc }}"
                    style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="Localização"></iframe>
        </div>
        @endif
    </div>
</div>

@endsection
