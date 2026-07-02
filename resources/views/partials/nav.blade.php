@php $t = config('tenant'); @endphp

<div id="topo">
    <div id="topocentro">
        <li>
            <i class="fa fa-envelope icon-topo"></i>
            <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a>
        </li>
        <li>
            <i class="fa-brands fa-whatsapp icon-topo"></i>
            <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" target="_blank" rel="noopener">
                {{ $t['whatsapp_exibir'] }}
            </a>
        </li>

        <div class="social-icons">
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
    </div>
</div>

<div class="header" id="home">
    <div class="header-top">
        <div class="container">

            <div class="logo logo--{{ $t['logo_header'] }}">
                <h1>
                    <a href="{{ route('home') }}" class="logo-link">
                        @if(in_array($t['logo_header'], ['logo', 'ambos']))
                        <img src="{{ asset($t['logo']) }}" alt="{{ $t['nome'] }}" class="logo-img">
                        @endif
                        @if(in_array($t['logo_header'], ['texto', 'ambos']))
                        <span class="logo-text">{{ $t['nome'] }}</span>
                        @endif
                    </a>
                </h1>
            </div>

            <div class="header-bottom">
                <nav class="navbar navbar-expand-lg navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggler"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#navMenu"
                                    aria-controls="navMenu"
                                    aria-expanded="false">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <div class="collapse navbar-collapse" id="navMenu">
                            <nav id="menu" class="menu menu--francisco">
                                <ul class="nav navbar-nav menu__list">

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
                                        <li class="menu__item dropdown {{ $filhoAtivo ? 'menu__item--current' : '' }}">
                                            <a href="#" class="menu__link dropdown-toggle"
                                               data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="menu__helper">{{ $item['label'] }}</span>
                                            </a>
                                            <ul class="dropdown-menu">
                                                @foreach($item['filhos'] as $filho)
                                                @if($filho['ativo'])
                                                <li>
                                                    @if($filho['tipo'] === 'servico')
                                                        <a href="{{ route('servico', $filho['rota']) }}"
                                                           class="@active('servico', ['slug' => $filho['rota']])">
                                                            {{ $filho['label'] }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route($filho['rota']) }}"
                                                           class="@active($filho['rota'])">
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
                                            $itemAtivo = $item['tipo'] === 'servico'
                                                ? (request()->routeIs('servico') && (string) request()->route('slug') === (string) $item['rota'])
                                                : request()->routeIs($item['rota']);
                                        @endphp
                                        <li class="menu__item {{ $itemAtivo ? 'menu__item--current' : '' }}">
                                            @if($item['tipo'] === 'servico')
                                                <a href="{{ route('servico', $item['rota']) }}" class="menu__link">
                                                    <span class="menu__helper">{{ $item['label'] }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route($item['rota']) }}" class="menu__link">
                                                    <span class="menu__helper">{{ $item['label'] }}</span>
                                                </a>
                                            @endif
                                        </li>
                                    @endif

                                    @endif
                                    @endforeach

                                </ul>
                            </nav>
                        </div>
                    </div>
                </nav>
            </div>

        </div>
    </div>
</div>
