<?php

return [
    'slug'            => 'lider-vidros',
    'nome'            => 'Líder Vidros',
    'dominio'         => 'lidervidros.com.br',
    'cor_primaria'    => '#ed3237',
    'cor_secundaria'  => '#9e2016',
    'whatsapp'        => '5562983004326',
    'whatsapp_exibir' => '(62) 9 8300-4326',
    'email'           => 'comercial@lidervidros.com.br',
    'instagram'       => 'liderdosvidros',
    'facebook'        => 'liderdosvidros',
    'google_ads_id'   => null,
    'logo'            => '/tenants/lider-vidros/logo.png',
    'favicon'         => '/tenants/lider-vidros/favicon.png',
    'og_image'        => '/tenants/lider-vidros/og-image.jpeg',
    'endereco'        => 'Goiânia e região',
    'areas_atendidas' => ['Goiânia', 'Aparecida de Goiânia', 'Senador Canedo', 'Anápolis'],
    'schema_type'     => 'LocalBusiness',

    'seo' => [
        'title_padrao' => 'Vidraçaria em Goiânia | Líder Vidros — Box, Espelhos e Esquadrias',
        'description'  => 'Líder Vidros: especialistas em vidraçaria em Goiânia. Box para banheiro, espelhos sob medida, esquadrias de alumínio e portas de vidro.',
        'keywords'     => 'vidraçaria em goiânia, box banheiro, espelhos sob medida, esquadrias de alumínio, líder vidros',
    ],

    'paginas' => [
        'home' => [
            'titulo_hero'    => 'Vidraçaria em Goiânia',
            'subtitulo_hero' => 'Box banheiro, espelhos e esquadrias de alumínio',
        ],
        'sobre' => [
            'titulo'    => 'Líder Vidros',
            'descricao' => 'A Líder Vidros atua há anos no mercado de vidraçaria em Goiânia, oferecendo soluções completas em vidros e alumínio.',
            'missao'    => 'Oferecer produtos de qualidade com excelente atendimento.',
            'visao'     => 'Ser a vidraçaria mais reconhecida de Goiânia.',
            'valores'   => 'Qualidade, confiança e compromisso com o cliente.',
        ],
    ],

    'servicos' => [
        ['slug' => 'vidros',   'titulo' => 'Espelhos e Vidraçaria',  'ativo' => true],
        ['slug' => 'aluminio', 'titulo' => 'Esquadrias e Alumínio',  'ativo' => true],
        ['slug' => 'cortina',  'titulo' => 'Cortinas',               'ativo' => true],
    ],

    'features' => [
        [
            'titulo'    => 'Películas',
            'descricao' => 'Redução de calor, proteção contra raios UV, controle de luminosidade, economia de energia, segurança e sofisticação.',
            'imagens'   => ['f1.jpg', 'f2.jpg', 'f3.jpg'],
            'tipo'      => 'servico',
            'rota'      => 'vidros',
        ],
        [
            'titulo'    => 'Guarda-corpo',
            'descricao' => 'São colocados em escadas, bordas de sacadas, rampas, passarelas e mezaninos.',
            'imagens'   => ['f4.jpg'],
            'tipo'      => 'servico',
            'rota'      => 'aluminio',
        ],
    ],

    'menu' => [
        ['tipo' => 'pagina', 'rota' => 'home',  'label' => 'Início',  'ativo' => true],
        ['tipo' => 'pagina', 'rota' => 'sobre', 'label' => 'Empresa', 'ativo' => true],
        ['tipo' => 'dropdown', 'label' => 'Serviços', 'ativo' => true, 'filhos' => [
            ['tipo' => 'servico', 'rota' => 'vidros',   'label' => 'Espelhos e Vidraçaria',  'ativo' => true],
            ['tipo' => 'servico', 'rota' => 'aluminio', 'label' => 'Esquadrias e Alumínio',  'ativo' => true],
            ['tipo' => 'pagina',  'rota' => 'catalogo', 'label' => 'Catálogo de Serviços',   'ativo' => true],
        ]],
        ['tipo' => 'pagina', 'rota' => 'contato', 'label' => 'Contato', 'ativo' => true],
    ],

    'slides_count' => 3,
];
