@extends('layouts.app')
@php $t = config('tenant'); @endphp

@section('title', $servico['titulo'] . ' — ' . $t['nome'])

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe.css">
<style>
.pswp-caption-content {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: .5em 1.2em;
    background: linear-gradient(transparent, rgba(0,0,0,.65));
    color: #fff;
    font-size: .9em;
    text-align: center;
    pointer-events: none;
}
</style>
@endsection

@section('content')

<div class="banner1 banner-servicos">
    <div class="dot"></div>
    <div class="container">
        <h3>
            <a href="{{ route('home') }}">Início</a> /
            <span>{{ $servico['titulo'] }}</span>
        </h3>
    </div>
</div>

<div class="gallery-w3l">
    <div class="container">
        <h2 class="tittle">{{ $servico['titulo'] }}</h2>

        <div class="pswp-gallery gallery-grids row" id="pswp-servico">
            @if($images->count())
                @foreach($images as $i => $img)
                @php $url = asset('storage/' . $img->path); $caption = $img->titulo ?: $servico['titulo']; @endphp
                <div class="col-sm-6 gallery-grids-left {{ $i >= 2 ? 'pro-top' : '' }}">
                    <div class="multi-gd-text">
                        <a href="{{ $url }}"
                           class="pswp-item"
                           data-pswp-caption="{{ $caption }}">
                            <div class="port-7 effect-3">
                                @if($img->titulo)<p>{{ $img->titulo }}</p>@endif
                                <img src="{{ $url }}"
                                     alt="{{ $caption }}"
                                     loading="{{ $i < 2 ? 'eager' : 'lazy' }}">
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <p>Conteúdo em breve.</p>
                </div>
            @endif
        </div>

        <div class="text-center mt-5">
            <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}"
               class="btn btn-primary btn-lg me-2" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
            </a>
            <a href="{{ route('contato') }}" class="btn btn-outline-primary btn-lg">
                Formulário de Contato
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="module">
import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe-lightbox.esm.min.js';

// Lê naturalWidth/Height de cada <img> já carregado na página e escreve nos <a>
// (thumbnail e full têm a mesma URL para imagens locais)
function hydrateDims(galleryId) {
    document.querySelectorAll(galleryId + ' a.pswp-item').forEach(link => {
        if (link.dataset.pswpWidth) return;
        const img = link.querySelector('img');
        if (!img) return;
        const set = () => {
            if (img.naturalWidth > 0) {
                link.dataset.pswpWidth  = img.naturalWidth;
                link.dataset.pswpHeight = img.naturalHeight;
            }
        };
        img.complete ? set() : img.addEventListener('load', set, { once: true });
    });
}

hydrateDims('#pswp-servico');

const lightbox = new PhotoSwipeLightbox({
    gallery:        '#pswp-servico',
    children:       'a.pswp-item',
    pswpModule:     () => import('https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe.esm.min.js'),
    bgOpacity:      0.92,
    zoom:           true,
    loop:           true,
    preloaderDelay: 0,
});

// Fallback para imagens lazy que ainda não foram carregadas ao abrir
lightbox.on('contentLoad', (e) => {
    const { content } = e;
    if (content.type !== 'image' || content.data.w) return;
    e.preventDefault();
    const imgEl = document.createElement('img');
    imgEl.className = 'pswp__img';
    imgEl.src = content.data.src;
    content.element = imgEl;
    content.state   = 'loading';
    imgEl.addEventListener('load', () => {
        content.data.w = imgEl.naturalWidth;
        content.data.h = imgEl.naturalHeight;
        content.state  = 'loaded';
        content.slide?.updateContentSize(true);
    }, { once: true });
    imgEl.addEventListener('error', () => (content.state = 'error'), { once: true });
});

// Caption flutuante
lightbox.on('uiRegister', () => {
    lightbox.pswp.ui.registerElement({
        name:     'caption',
        order:    9,
        isButton: false,
        appendTo: 'root',
        onInit: (el, pswp) => {
            pswp.on('change', () => {
                const links = document.querySelectorAll('#pswp-servico a.pswp-item');
                const text  = links[pswp.currIndex]?.dataset?.pswpCaption || '';
                el.innerHTML = text ? `<div class="pswp-caption-content">${text}</div>` : '';
            });
        },
    });
});

lightbox.init();
</script>
@endsection
