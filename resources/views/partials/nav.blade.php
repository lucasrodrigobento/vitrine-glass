@php $t = config('tenant'); @endphp

<div id="topo">
    <div id="topocentro">
        <ul>
            <li>
                <a href="mailto:{{ $t['email'] }}">
                    <i class="fa fa-envelope"></i>{{ $t['email'] }}
                </a>
            </li>
            <li>
                <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i>{{ $t['whatsapp_exibir'] }}
                </a>
            </li>
        </ul>
        <div class="topo-social">
            @if(!empty($t['facebook']))
            <a href="https://www.facebook.com/{{ $t['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook">
                <i class="fa-brands fa-facebook"></i>
            </a>
            @endif
            @if(!empty($t['instagram']))
            <a href="https://www.instagram.com/{{ $t['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram">
                <i class="fa-brands fa-instagram"></i>
            </a>
            @endif
            <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="header">
    <div class="header-top">
        <div class="container">
            <div class="logo">
                <h1>
                    <a href="{{ route('home') }}" class="logo-link">
                        <img src="{{ asset($t['logo']) }}" alt="{{ $t['nome'] }}">
                        <span class="logo-text">{{ $t['nome'] }}</span>
                    </a>
                </h1>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="header-bottom">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-default" role="navigation">
                <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navMenu"
                    aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav menu--francisco">
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
                            <li class="nav-item dropdown menu__item {{ $filhoAtivo ? 'menu__item--current' : '' }}">
                                <a class="nav-link menu__link dropdown-toggle"
                                   href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="menu__helper">{{ $item['label'] }}</span>
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

                        @else
                            @php
                                if ($item['tipo'] === 'servico') {
                                    $itemAtivo = request()->routeIs('servico')
                                        && (string) request()->route('slug') === (string) $item['rota'];
                                } else {
                                    $itemAtivo = request()->routeIs($item['rota']);
                                }
                            @endphp
                            <li class="nav-item menu__item {{ $itemAtivo ? 'menu__item--current' : '' }}">
                                @if($item['tipo'] === 'servico')
                                    <a class="nav-link menu__link" href="{{ route('servico', $item['rota']) }}">
                                        <span class="menu__helper">{{ $item['label'] }}</span>
                                    </a>
                                @else
                                    <a class="nav-link menu__link" href="{{ route($item['rota']) }}">
                                        <span class="menu__helper">{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            </li>
                        @endif

                        @endif
                        @endforeach
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>
