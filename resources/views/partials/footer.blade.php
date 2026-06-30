@php $t = config('tenant'); @endphp

<div class="footer-w3l">
    <div class="container">
        <div class="footer-grids row">
            <div class="col-md-4 footer-grid">
                <h4>Contate-nos</h4>
                @if(!empty($t['facebook']))
                <p>
                    <a href="https://www.facebook.com/{{ $t['facebook'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-facebook me-2"></i>/{{ $t['facebook'] }}
                    </a>
                </p>
                @endif
                @if(!empty($t['instagram']))
                <p>
                    <a href="https://www.instagram.com/{{ $t['instagram'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-instagram me-2"></i>{{ '@' . $t['instagram'] }}
                    </a>
                </p>
                @endif
                <p>
                    <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp me-2"></i>{{ $t['whatsapp_exibir'] }}
                    </a>
                </p>
                <p>
                    <a href="mailto:{{ $t['email'] }}">
                        <i class="fa fa-envelope me-2"></i>{{ $t['email'] }}
                    </a>
                </p>
            </div>

            <div class="col-md-4 footer-grid">
                <h4>Endereço</h4>
                <p>{{ $t['endereco'] }}</p>
            </div>

            <div class="col-md-4 footer-grid">
                <h4>Navegação</h4>
                <ul>
                    @foreach(config('tenant.menu', []) as $item)
                    @if($item['ativo'])
                        @if($item['tipo'] === 'dropdown')
                            @foreach($item['filhos'] as $filho)
                            @if($filho['ativo'])
                            <li>
                                @if($filho['tipo'] === 'servico')
                                    <a href="{{ route('servico', $filho['rota']) }}">{{ $filho['label'] }}</a>
                                @else
                                    <a href="{{ route($filho['rota']) }}">{{ $filho['label'] }}</a>
                                @endif
                            </li>
                            @endif
                            @endforeach
                        @elseif($item['tipo'] === 'servico')
                            <li><a href="{{ route('servico', $item['rota']) }}">{{ $item['label'] }}</a></li>
                        @else
                            <li><a href="{{ route($item['rota']) }}">{{ $item['label'] }}</a></li>
                        @endif
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="copy-section">
    <div class="container">
        <p>© {{ date('Y') }} {{ $t['nome'] }}. Todos os direitos reservados.</p>
    </div>
</div>
