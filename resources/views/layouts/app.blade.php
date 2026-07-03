@php use Illuminate\Support\Facades\Storage; $t = config('tenant'); @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Preconnects --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">

    <title>@yield('title', $t['seo']['title_padrao'])</title>
    <meta name="description" content="@yield('description', $t['seo']['description'])">
    @if(!empty($t['seo']['keywords']))
    <meta name="keywords" content="{{ $t['seo']['keywords'] }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type"         content="website">
    <meta property="og:locale"       content="pt_BR">
    <meta property="og:site_name"    content="{{ $t['nome'] }}">
    <meta property="og:title"        content="@yield('title', $t['seo']['title_padrao'])">
    <meta property="og:description"  content="@yield('description', $t['seo']['description'])">
    <meta property="og:image"        content="{{ asset($t['og_image']) }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url"          content="{{ url()->current() }}">

    {{-- Twitter / X --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('title', $t['seo']['title_padrao'])">
    <meta name="twitter:description" content="@yield('description', $t['seo']['description'])">
    <meta name="twitter:image"       content="{{ asset($t['og_image']) }}">

    <link rel="shortcut icon" href="{{ asset($t['favicon']) }}">

    {{-- Schema.org LocalBusiness --}}
    @php
        $baseUrl = rtrim(url('/'), '/');
        $socialProfiles = array_values(array_filter([
            !empty($t['facebook'])  ? "https://www.facebook.com/{$t['facebook']}"   : null,
            !empty($t['instagram']) ? "https://www.instagram.com/{$t['instagram']}" : null,
        ]));
        $schema = array_filter([
            '@context'    => 'https://schema.org',
            '@type'       => $t['schema_type'] ?: 'LocalBusiness',
            '@id'         => $baseUrl . '/#negocio',
            'name'        => $t['nome'],
            'url'         => $baseUrl,
            'description' => $t['seo']['description'] ?: null,
            'telephone'   => '+' . $t['whatsapp'],
            'email'       => $t['email'] ?: null,
            'image'       => ['@type' => 'ImageObject', 'url' => asset($t['og_image'])],
            'logo'        => ['@type' => 'ImageObject', 'url' => asset($t['logo'])],
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $t['areas_atendidas'][0] ?? 'Goiânia',
                'addressRegion'   => 'GO',
                'addressCountry'  => 'BR',
            ],
            'areaServed'  => $t['areas_atendidas'] ?: null,
            'sameAs'      => $socialProfiles ?: null,
            'hasMap'      => !empty($t['google_maps_query'])
                ? 'https://maps.google.com/maps?q=' . urlencode($t['google_maps_query'])
                : null,
            'priceRange'  => '$$',
        ]);
        $primaryTagId = $t['google_ads_id'] ?? $t['google_analytics_id'] ?? null;
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>

    {{-- Google Ads / GA4 — carrega somente se o tenant tiver ID configurado --}}
    @if($primaryTagId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $primaryTagId }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    @if(!empty($t['google_ads_id']))
    gtag('config', '{{ $t['google_ads_id'] }}');
    @endif
    @if(!empty($t['google_analytics_id']))
    gtag('config', '{{ $t['google_analytics_id'] }}');
    @endif
    </script>
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @if(!empty($t['fonts']['google_url']))
    <link href="{{ $t['fonts']['google_url'] }}" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @yield('styles')

    <style>
        :root {
            --cor-primaria:     {{ $t['cor_primaria'] }};
            --cor-secundaria:   {{ $t['cor_secundaria'] }};
            --cor-texto:        {{ $t['cor_texto'] ?? '#6f7070' }};
            --cor-rodape-fundo: {{ $t['cor_rodape_fundo'] ?? 'var(--cor-primaria)' }};
            --cor-rodape-links: {{ $t['cor_rodape_links'] ?? 'var(--cor-primaria)' }};
            --font-body:        '{{ $t['fonts']['body'] ?? 'Open Sans' }}';
            --font-heading:     '{{ $t['fonts']['heading'] ?? 'Righteous' }}';
            --font-accent:      '{{ $t['fonts']['accent'] ?? 'Josefin Sans' }}';
        }
    </style>
</head>
<body>
    @include('partials.nav')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}"
       class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    @if($primaryTagId)
    <script>
    (function () {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href*="api.whatsapp.com"]');
            if (link && typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', { event_category: 'contato' });
                @if(!empty($t['google_ads_whatsapp_label']))
                gtag('event', 'conversion', { send_to: '{{ $t['google_ads_whatsapp_label'] }}' });
                @endif
            }
        });
        @if(session('sucesso'))
        typeof gtag !== 'undefined' && gtag('event', 'form_submit', {
            event_category: 'contato',
            event_label: 'formulario_contato'
        });
        @endif
    })();
    </script>
    @endif
</body>
</html>
