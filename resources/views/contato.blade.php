@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', 'Contato — ' . $t['nome'])

@section('content')

<div class="page-banner banner-contato">
    <div class="container">
        <h1>Fale Conosco</h1>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                @if(session('sucesso'))
                <div class="alert alert-success">{{ session('sucesso') }}</div>
                @endif

                <form method="POST" action="{{ route('contato.enviar') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                               value="{{ old('nome') }}" required>
                        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" rows="5" class="form-control @error('mensagem') is-invalid @enderror"
                                  required>{{ old('mensagem') }}</textarea>
                        @error('mensagem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                </form>
            </div>

            <div class="col-md-5 offset-md-1 mt-4 mt-md-0">
                <h4>Canais de Atendimento</h4>
                <p>
                    <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-brands fa-whatsapp fa-lg"></i> {{ $t['whatsapp_exibir'] }}
                    </a>
                    <a href="mailto:{{ $t['email'] }}" class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa fa-envelope fa-lg"></i> {{ $t['email'] }}
                    </a>
                    <a href="https://www.instagram.com/{{ $t['instagram'] }}" class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-brands fa-instagram fa-lg"></i> {{ '@' . $t['instagram'] }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
