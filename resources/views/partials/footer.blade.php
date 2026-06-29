@php $t = config('tenant'); @endphp

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Contate-nos</h5>
                <p>
                    <a href="https://www.facebook.com/{{ $t['facebook'] }}">
                        <i class="fa-brands fa-facebook me-2"></i>/{{ $t['facebook'] }}
                    </a>
                </p>
                <p>
                    <a href="https://www.instagram.com/{{ $t['instagram'] }}">
                        <i class="fa-brands fa-instagram me-2"></i>{{ '@' . $t['instagram'] }}
                    </a>
                </p>
                <p>
                    <a href="https://api.whatsapp.com/send?phone={{ $t['whatsapp'] }}">
                        <i class="fa-brands fa-whatsapp me-2"></i>{{ $t['whatsapp_exibir'] }}
                    </a>
                </p>
                <p>
                    <a href="mailto:{{ $t['email'] }}">
                        <i class="fa fa-envelope me-2"></i>{{ $t['email'] }}
                    </a>
                </p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Endereço</h5>
                <p>{{ $t['endereco'] }}</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Navegação</h5>
                <ul class="list-unstyled">
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
</footer>

<div class="footer-copy">
    <div class="container text-center">
        <p>© {{ date('Y') }} {{ $t['nome'] }}. Todos os direitos reservados.</p>
    </div>
</div>
