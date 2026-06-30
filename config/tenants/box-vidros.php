<?php

return [
    'slug'            => 'box-vidros',
    'nome'            => 'Box e Vidros Goiânia',
    'dominio'         => 'boxevidros.com.br',
    'cor_primaria'    => '#183b9f',
    'cor_secundaria'  => '#1a2a6c',
    'whatsapp'        => '5562981627155',
    'whatsapp_exibir' => '(62) 9 8162-7155',
    'email'           => 'contato@boxevidros.com.br',
    'instagram'       => 'boxevidrosgyn',
    'facebook'        => 'boxevidrosgyn',
    'google_ads_id'      => null,
    'google_maps_embed'  => null,
    'logo'            => '/tenants/box-vidros/logo.png',
    'favicon'         => '/tenants/box-vidros/favicon.png',
    'og_image'        => '/tenants/box-vidros/og-image.jpeg',
    'endereco'        => 'Goiânia e região',
    'areas_atendidas' => ['Goiânia', 'Aparecida de Goiânia', 'Senador Canedo', 'Anápolis', 'Goianira'],
    'schema_type'     => 'LocalBusiness',

    'seo' => [
        'title_padrao' => 'Vidraçaria em Goiânia | Box Banheiro, Espelhos, Esquadrias e Portas de Vidro',
        'description'  => 'Vidraçaria em Goiânia especializada em box banheiro, espelhos sob medida, portas de vidro, esquadrias de alumínio e fechamento de sacada.',
        'keywords'     => 'vidraçaria em goiânia, box banheiro goiânia, espelhos sob medida, esquadrias de alumínio, portas de vidro',
    ],

    'paginas' => [
        'home' => [
            'titulo_hero'    => 'Vidraçaria em Goiânia',
            'subtitulo_hero' => 'Box banheiro, espelhos, esquadrias e portas de vidro',
        ],
        'sobre' => [
            'titulo'    => 'Box e Vidros Goiânia',
            'descricao' => 'Atuando há mais de 7 anos no mercado, a Box e Vidros Goiânia continua buscando ser referência no segmento de VIDRAÇARIA em Goiânia e região.',
            'missao'    => 'Oferecer atendimento e produtos de qualidade aos nossos clientes.',
            'visao'     => 'Ser referência no segmento de VIDRAÇARIA em Goiânia e região.',
            'valores'   => 'Seriedade, transparência e, sobretudo, respeito.',
        ],
    ],

    'servicos' => [
        ['slug' => 'vidros',   'titulo' => 'Espelhos e Vidraçaria',   'ativo' => true],
        ['slug' => 'aluminio', 'titulo' => 'Esquadrias de Alumínio',  'ativo' => true],
        ['slug' => 'cortina',  'titulo' => 'Cortinas e Acessórios',   'ativo' => true],
    ],

    'features' => [
        [
            'titulo'    => 'Espelhos e Vidros',
            'descricao' => 'Espelhos sob medida, vidros temperados e decorativos com instalação profissional e acabamento perfeito para sua casa ou empresa.',
            'imagens'   => ['f1.jpg', 'f2.jpg', 'f3.jpg'],
            'tipo'      => 'servico',
            'rota'      => 'vidros',
        ],
        [
            'titulo'    => 'Esquadrias de Alumínio',
            'descricao' => 'Janelas, portas e esquadrias de alumínio com vidro temperado. Projetos personalizados e instalação completa.',
            'imagens'   => ['f4.jpg'],
            'tipo'      => 'servico',
            'rota'      => 'aluminio',
        ],
    ],

    'menu' => [
        ['tipo' => 'pagina', 'rota' => 'home',     'label' => 'Início',   'ativo' => true],
        ['tipo' => 'pagina', 'rota' => 'sobre',    'label' => 'Empresa',  'ativo' => true],
        ['tipo' => 'pagina', 'rota' => 'catalogo', 'label' => 'Serviços', 'ativo' => true],
        ['tipo' => 'pagina', 'rota' => 'contato',  'label' => 'Contato',  'ativo' => true],
    ],

    'slides_count' => 4,
];
