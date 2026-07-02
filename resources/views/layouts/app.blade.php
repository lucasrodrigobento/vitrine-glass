@php use Illuminate\Support\Facades\Storage; $t = config('tenant'); @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $t['seo']['title_padrao'])</title>
    <meta name="description" content="@yield('description', $t['seo']['description'])">
    <meta name="keywords"    content="{{ $t['seo']['keywords'] }}">

    <meta property="og:title"       content="@yield('title', $t['seo']['title_padrao'])">
    <meta property="og:description" content="@yield('description', $t['seo']['description'])">
    <meta property="og:image"       content="{{ asset($t['og_image']) }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:type"        content="website">

    <link rel="canonical"    href="{{ url()->current() }}">
    <link rel="shortcut icon" href="{{ asset($t['favicon']) }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @if(!empty($t['fonts']['google_url']))
    <link href="{{ $t['fonts']['google_url'] }}" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @yield('styles')

    <style>
        :root {
            --cor-primaria:   {{ $t['cor_primaria'] }};
            --cor-secundaria: {{ $t['cor_secundaria'] }};
            --font-body:      '{{ $t['fonts']['body'] ?? 'Open Sans' }}';
            --font-heading:   '{{ $t['fonts']['heading'] ?? 'Righteous' }}';
            --font-accent:    '{{ $t['fonts']['accent'] ?? 'Josefin Sans' }}';
        }
    </style>

    @php
    $schema = json_encode([
        '@context'   => 'https://schema.org',
        '@type'      => $t['schema_type'],
        'name'       => $t['nome'],
        'image'      => asset($t['logo']),
        'telephone'  => '+55 ' . preg_replace('/^55/', '', $t['whatsapp']),
        'address'    => ['@type' => 'PostalAddress', 'addressLocality' => 'Goiânia', 'addressRegion' => 'GO', 'addressCountry' => 'BR'],
        'areaServed' => $t['areas_atendidas'],
    ]);
    @endphp
    <script type="application/ld+json">{!! $schema !!}</script>
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
</body>
</html>
