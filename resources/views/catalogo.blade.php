@extends('layouts.app')
@php $t = config('tenant'); $isDrive = ($mode === 'drive'); @endphp

@section('title', 'Catálogo — ' . $t['nome'])

@if($isDrive)
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">
@endsection
@endif

@section('content')

<div class="banner1 banner-servicos">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>Catálogo de Serviços</span>
        </h3>
    </div>
</div>

<div class="gallery-w3l">
    <div class="container">
        <h2 class="tittle">Catálogo de Serviços</h2>
        <div class="gallery-grids row">

            @if($isDrive)

                {{-- Modo Google Drive: thumbnail lazy + lightbox --}}
                @forelse($images as $file)
                <div class="col-md-4 col-sm-6 col-12 gallery-grids-left mb-4">
                    <a href="{{ $drive->fullUrl($file['id']) }}"
                       class="glightbox"
                       data-gallery="catalogo"
                       data-title="{{ $file['name'] }}">
                        <div class="image-box-catalogo" data-src="{{ $drive->thumbUrl($file['id']) }}">
                            <div class="img-loader"></div>
                            <img class="img-catalogo img-fluid"
                                 alt="Catálogo — {{ $t['nome'] }}"
                                 loading="lazy">
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p>Nenhuma imagem encontrada no catálogo.</p>
                </div>
                @endforelse

            @else

                {{-- Modo local: imagens do banco --}}
                @forelse($images as $img)
                <div class="col-md-4 col-sm-6 col-12 gallery-grids-left mb-4">
                    <div class="gallery-grid">
                        <img src="{{ asset('storage/' . $img->path) }}"
                             class="img-fluid"
                             alt="{{ $img->titulo ?: $t['nome'] }}"
                             loading="lazy">
                        @if($img->titulo)
                        <p class="text-center mt-1 small">{{ $img->titulo }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p>Nenhuma imagem cadastrada ainda.</p>
                </div>
                @endforelse

            @endif

        </div>

        @if($images->hasPages())
        <div class="text-center mt-4">
            {{ $images->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('styles')
@parent
<style>
.image-box-catalogo {
    width: 100%;
    height: 260px;
    overflow: hidden;
    border-radius: 6px;
    position: relative;
    background: #f0f0f0 center/cover no-repeat;
    cursor: pointer;
    transition: transform .2s;
}
.image-box-catalogo:hover {
    transform: scale(1.02);
}
.image-box-catalogo .img-catalogo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}
.image-box-catalogo .img-catalogo.loaded {
    display: block;
}
.img-loader {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.img-loader::after {
    content: "";
    width: 32px;
    height: 32px;
    border: 3px solid #ddd;
    border-top-color: var(--cor-primaria);
    border-radius: 50%;
    animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@if($isDrive)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lazy load das thumbnails
    document.querySelectorAll('.image-box-catalogo').forEach(function (box) {
        var src    = box.dataset.src;
        var imgTag = box.querySelector('.img-catalogo');
        var loader = box.querySelector('.img-loader');
        var temp   = new Image();
        temp.onload = function () {
            imgTag.src = src;
            imgTag.classList.add('loaded');
            loader.style.display = 'none';
        };
        temp.onerror = function () {
            loader.style.display = 'none';
        };
        temp.src = src;
    });

    // Lightbox
    GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });
});
</script>
@endsection
@endif
