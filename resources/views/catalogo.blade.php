@extends('layouts.app')
@php $t = config('tenant'); $isDrive = ($mode === 'drive'); @endphp

@section('title', 'Catálogo de Serviços — ' . $t['nome'])

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
.image-box-catalogo {
    width: 100%;
    height: 320px;
    overflow: hidden;
    border-radius: 4px;
    position: relative;
    background: #e8e8e8 center/cover no-repeat;
}
.image-box-catalogo .img-catalogo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}
.image-box-catalogo .img-catalogo.loaded { display: block; }
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
    width: 35px; height: 35px;
    border: 4px solid #ddd;
    border-top-color: var(--cor-primaria);
    border-radius: 50%;
    animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.gallery-grids-left { margin-bottom: 30px; }
</style>
@endsection

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

<div class="content">
    <div class="gallery-w3l">
        <div class="container">

            <div class="pswp-gallery row gallery-grids" id="pswp-catalogo">

                @forelse($images as $item)
                @php
                    if ($isDrive) {
                        $href    = $drive->fullUrl($item['id']);
                        $thumb   = $drive->thumbUrl($item['id']);
                        $caption = $item['name'];
                    } else {
                        $href    = asset('storage/' . $item->path);
                        $thumb   = $href;
                        $caption = $item->titulo ?: ('Catálogo — ' . $t['nome']);
                    }
                @endphp
                <div class="col-md-6 col-sm-6 col-12 gallery-grids-left">
                    <div class="multi-gd-text">
                        <a href="{{ $href }}"
                           class="pswp-item"
                           data-pswp-caption="{{ $caption }}">
                            <div class="port-7 effect-3">
                                @if($isDrive)
                                <div class="image-box-catalogo" data-src="{{ $thumb }}">
                                    <div class="img-loader"></div>
                                    <img class="img-catalogo" alt="{{ $caption }}">
                                </div>
                                @else
                                <div class="image-box-catalogo">
                                    <img class="img-catalogo loaded"
                                         src="{{ $thumb }}"
                                         alt="{{ $caption }}"
                                         loading="lazy">
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p>Nenhuma imagem no catálogo ainda.</p>
                </div>
                @endforelse

            </div>

            @if($images->hasPages())
            @php
                $cur   = $images->currentPage();
                $last  = $images->lastPage();
                $start = max(1, $cur - 4);
                $end   = min($last, $cur + 4);
            @endphp
            <div class="d-flex justify-content-center" style="margin-top: 30px;">
                <ul class="pagination">
                    @if($cur > 1)
                    <li class="page-item"><a class="page-link" href="{{ $images->url(1) }}">Início</a></li>
                    @endif
                    @if($start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $images->url(1) }}">1</a></li>
                    @if($start > 2)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                    @endif
                    @for($i = $start; $i <= $end; $i++)
                    <li class="page-item {{ $i === $cur ? 'active' : '' }}">
                        <a class="page-link" href="{{ $images->url($i) }}">{{ $i }}</a>
                    </li>
                    @endfor
                    @if($end < $last)
                    @if($end < $last - 1)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                    <li class="page-item"><a class="page-link" href="{{ $images->url($last) }}">{{ $last }}</a></li>
                    @endif
                    @if($cur < $last)
                    <li class="page-item"><a class="page-link" href="{{ $images->url($last) }}">Fim</a></li>
                    @endif
                </ul>
            </div>
            @endif

        </div>
    </div>
</div>

@endsection

@section('scripts')
@if($isDrive)
{{-- Lazy-load das thumbnails do Drive --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.image-box-catalogo[data-src]').forEach(box => {
        const src    = box.dataset.src;
        const imgTag = box.querySelector('.img-catalogo');
        const loader = box.querySelector('.img-loader');
        const temp   = new Image();
        temp.onload = () => {
            imgTag.src = src;
            imgTag.classList.add('loaded');
            if (loader) loader.style.display = 'none';
            // Aproveita as dimensões do thumbnail (mesmo aspect ratio do full)
            const link = box.closest('a.pswp-item');
            if (link && !link.dataset.pswpWidth && temp.naturalWidth > 0) {
                link.dataset.pswpWidth  = temp.naturalWidth;
                link.dataset.pswpHeight = temp.naturalHeight;
            }
        };
        temp.onerror = () => { if (loader) loader.style.display = 'none'; };
        temp.src = src;
    });
});
</script>
@endif

<script type="module">
import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe-lightbox.esm.min.js';

// Lê dimensões de imagens locais já carregadas
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

hydrateDims('#pswp-catalogo');

const lightbox = new PhotoSwipeLightbox({
    gallery:        '#pswp-catalogo',
    children:       'a.pswp-item',
    pswpModule:     () => import('https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe.esm.min.js'),
    bgOpacity:      0.92,
    zoom:           true,
    loop:           true,
    preloaderDelay: 0,
});

// Fallback: imagens cuja dimensão ainda não estava disponível no momento de hydrate
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

// Caption
lightbox.on('uiRegister', () => {
    lightbox.pswp.ui.registerElement({
        name:     'caption',
        order:    9,
        isButton: false,
        appendTo: 'root',
        onInit: (el, pswp) => {
            pswp.on('change', () => {
                const links = document.querySelectorAll('#pswp-catalogo a.pswp-item');
                const text  = links[pswp.currIndex]?.dataset?.pswpCaption || '';
                el.innerHTML = text ? `<div class="pswp-caption-content">${text}</div>` : '';
            });
        },
    });
});

lightbox.init();
</script>
@endsection
