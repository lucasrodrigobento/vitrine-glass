@php $t = config('tenant'); @endphp

<header class="site-header">
    <div class="topbar">
        <div class="container d-flex justify-content-between align-items-center py-1">
            <div class="topbar-contacts d-flex gap-3">
                <a href="mailto:{{ $t['email'] }}" class="topbar-item">
                    <i class="fa fa-envelope me-1"></i>{{ $t['email'] }}
                </a>
                <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" class="topbar-item">
                    <i class="fa-brands fa-whatsapp me-1"></i>{{ $t['whatsapp_exibir'] }}
                </a>
            </div>
            <div class="topbar-social d-flex gap-2">
                <a href="https://www.facebook.com/{{ $t['facebook'] }}" class="topbar-social-link" aria-label="Facebook">
                    <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="https://www.instagram.com/{{ $t['instagram'] }}" class="topbar-social-link" aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" class="topbar-social-link" aria-label="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset($t['logo']) }}" height="50" alt="{{ $t['nome'] }}">
                <span class="brand-name">{{ $t['nome'] }}</span>
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    @foreach(config('tenant.menu', []) as $item)
                        @if($item['ativo'])

                        @if($item['tipo'] === 'dropdown')
                            @php
                                $filhoAtivo = collect($item['filhos'])->contains(function ($filho) {
                                    if ($filho['tipo'] === 'servico') {
                                        return request()->routeIs('servico')
                                            && (string) request()->route('slug') === (string) $filho['rota'];
                                    }
                                    return request()->routeIs($filho['rota']);
                                });
                            @endphp
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ $filhoAtivo ? 'active' : '' }}"
                                   href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $item['label'] }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach($item['filhos'] as $filho)
                                        @if($filho['ativo'])
                                        <li>
                                            @if($filho['tipo'] === 'servico')
                                                <a class="dropdown-item @active('servico', ['slug' => $filho['rota']])"
                                                   href="{{ route('servico', $filho['rota']) }}">
                                                    {{ $filho['label'] }}
                                                </a>
                                            @else
                                                <a class="dropdown-item @active($filho['rota'])"
                                                   href="{{ route($filho['rota']) }}">
                                                    {{ $filho['label'] }}
                                                </a>
                                            @endif
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>

                        @elseif($item['tipo'] === 'servico')
                            <li class="nav-item">
                                <a class="nav-link @active('servico', ['slug' => $item['rota']])"
                                   href="{{ route('servico', $item['rota']) }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>

                        @else
                            <li class="nav-item">
                                <a class="nav-link @active($item['rota'])"
                                   href="{{ route($item['rota']) }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif

                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
