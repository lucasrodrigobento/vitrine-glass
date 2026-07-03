<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Cache;

class EmpresaResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Empresas';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Tabs::make()
                ->columnSpanFull()
                ->tabs([

                    Forms\Components\Tabs\Tab::make('Geral')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Forms\Components\TextInput::make('nome')
                                ->label('Nome da Empresa')
                                ->required()
                                ->maxLength(150)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('slug')
                                ->label('Slug (identificador único)')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->helperText('Ex: lider-vidros, box-vidros — sem espaços, minúsculo'),
                            Forms\Components\TextInput::make('dominio')
                                ->label('Domínio')
                                ->required()
                                ->maxLength(150)
                                ->unique(ignoreRecord: true)
                                ->helperText('Ex: lidervidros.com.br'),
                            Forms\Components\Toggle::make('ativo')
                                ->label('Empresa ativa')
                                ->default(true)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Cores & Identidade')
                        ->icon('heroicon-o-swatch')
                        ->schema([
                            Forms\Components\ColorPicker::make('cor_primaria')
                                ->label('Cor Primária')
                                ->required()
                                ->default('#ed3237')
                                ->helperText('Cor principal da marca — navbar, botões, destaques, links do rodapé'),
                            Forms\Components\ColorPicker::make('cor_secundaria')
                                ->label('Cor Secundária')
                                ->required()
                                ->default('#9e2016')
                                ->helperText('Cor complementar — gradientes, hovers'),
                            Forms\Components\ColorPicker::make('cor_texto')
                                ->label('Cor do Texto (body)')
                                ->default('#6f7070')
                                ->helperText('Cor dos parágrafos, topbar e texto geral do site'),
                            Forms\Components\ColorPicker::make('cor_rodape_fundo')
                                ->label('Cor de Fundo do Rodapé (barra copyright)')
                                ->helperText('Deixe em branco para usar a cor primária como fundo do rodapé'),
                            Forms\Components\ColorPicker::make('cor_rodape_links')
                                ->label('Cor dos Títulos e Links do Rodapé')
                                ->helperText('Títulos (h4) e links nas colunas do rodapé. Deixe em branco para usar a cor primária'),
                            Forms\Components\Select::make('logo_header')
                                ->label('Header: exibir')
                                ->options([
                                    'logo'  => 'Somente a logo (imagem)',
                                    'texto' => 'Somente o nome da empresa (texto)',
                                    'ambos' => 'Logo + nome da empresa',
                                ])
                                ->default('logo')
                                ->required()
                                ->columnSpanFull()
                                ->helperText('Define o que aparece no canto esquerdo do header'),
                            Forms\Components\FileUpload::make('logo')
                                ->label('Logo')
                                ->image()
                                ->directory('tenants/logos')
                                ->imageResizeMode('contain')
                                ->imageResizeTargetWidth('400')
                                ->imageResizeTargetHeight('200')
                                ->maxSize(2048)
                                ->helperText('PNG com fundo transparente recomendado. Máx 2MB'),
                            Forms\Components\FileUpload::make('favicon')
                                ->label('Favicon')
                                ->image()
                                ->directory('tenants/favicons')
                                ->imageResizeTargetWidth('64')
                                ->imageResizeTargetHeight('64')
                                ->maxSize(512)
                                ->helperText('ICO ou PNG 64×64px'),
                            Forms\Components\FileUpload::make('og_image')
                                ->label('Imagem Open Graph (compartilhamento)')
                                ->image()
                                ->directory('tenants/og-images')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('1200:630')
                                ->imageResizeTargetWidth('1200')
                                ->imageResizeTargetHeight('630')
                                ->maxSize(2048)
                                ->columnSpanFull()
                                ->helperText('Imagem exibida ao compartilhar o link no WhatsApp/redes. 1200×630px'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Contato')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Forms\Components\TextInput::make('whatsapp')
                                ->label('WhatsApp (código internacional)')
                                ->required()
                                ->maxLength(20)
                                ->helperText('Ex: 5562983004326 — sem espaços ou símbolos'),
                            Forms\Components\TextInput::make('whatsapp_exibir')
                                ->label('WhatsApp (exibição)')
                                ->required()
                                ->maxLength(30)
                                ->helperText('Ex: (62) 9 8300-4326'),
                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->required()
                                ->maxLength(150),
                            Forms\Components\TextInput::make('instagram')
                                ->label('Instagram (sem @)')
                                ->maxLength(100)
                                ->prefix('@'),
                            Forms\Components\TextInput::make('facebook')
                                ->label('Facebook (usuário ou página)')
                                ->maxLength(100)
                                ->prefix('facebook.com/'),
                            Forms\Components\TextInput::make('endereco')
                                ->label('Endereço / Área de Atendimento')
                                ->maxLength(255),
                            Forms\Components\TagsInput::make('areas_atendidas')
                                ->label('Cidades Atendidas')
                                ->columnSpanFull()
                                ->helperText('Pressione Enter após cada cidade'),
                            Forms\Components\TextInput::make('google_ads_id')
                                ->label('ID Google Ads')
                                ->maxLength(50)
                                ->placeholder('AW-XXXXXXXXX')
                                ->helperText('Tag de conversão do Google Ads. Encontrado em google.com/ads → Ferramentas → Tag do Google'),
                            Forms\Components\TextInput::make('google_analytics_id')
                                ->label('ID Google Analytics 4 (GA4)')
                                ->maxLength(30)
                                ->placeholder('G-XXXXXXXXXX')
                                ->helperText('Measurement ID do GA4. Em analytics.google.com → Propriedade → Fluxos de dados → ID de medição'),
                            Forms\Components\TextInput::make('google_maps_query')
                                ->label('Localização no Google Maps (endereço ou nome do local)')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->placeholder('Ex: Rua das Flores, 123 – Goiânia, GO')
                                ->helperText('Digite um endereço ou nome do estabelecimento — o mapa será gerado automaticamente. Deixe em branco para ocultar o mapa.'),
                            Forms\Components\Textarea::make('google_maps_embed')
                                ->label('Embed Google Maps (URL avançada — opcional)')
                                ->rows(2)
                                ->columnSpanFull()
                                ->helperText('Use somente se precisar de um ponto exato. Gere em Google Maps → Compartilhar → Incorporar → copie apenas o valor do atributo src. Se "Localização" estiver preenchida acima, este campo é ignorado.'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('SEO')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')
                                ->label('Título da Página (SEO)')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->helperText('Recomendado: 50-60 caracteres'),
                            Forms\Components\Textarea::make('seo_description')
                                ->label('Meta Description')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull()
                                ->helperText('Recomendado: 150-160 caracteres'),
                            Forms\Components\Textarea::make('seo_keywords')
                                ->label('Palavras-chave')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull()
                                ->helperText('Separadas por vírgula'),
                            Forms\Components\Select::make('schema_type')
                                ->label('Tipo de Negócio (Schema.org)')
                                ->options([
                                    'LocalBusiness'   => 'Negócio Local',
                                    'HomeAndConstruction' => 'Construção e Casa',
                                    'Store'           => 'Loja',
                                    'Service'         => 'Serviço',
                                    'Organization'    => 'Organização',
                                ])
                                ->default('LocalBusiness'),
                        ])->columns(1),

                    Forms\Components\Tabs\Tab::make('Página Inicial')
                        ->icon('heroicon-o-home')
                        ->schema([
                            Forms\Components\Section::make('Hero (Banner Principal)')
                                ->description('Título e subtítulo globais — usados como fallback nos slides que não tiverem texto próprio')
                                ->schema([
                                    Forms\Components\TextInput::make('hero_titulo')
                                        ->label('Título padrão do Hero')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('hero_subtitulo')
                                        ->label('Subtítulo padrão do Hero')
                                        ->maxLength(255),
                                ])->columns(2),

                            Forms\Components\Section::make('Slides do Hero')
                                ->description('⚠️ Imagens devem ter no mínimo 1440×810px. Recomendado: 1920×1080px (Full HD). Imagens menores ficam desfocadas em monitores grandes.')
                                ->schema([
                                    Forms\Components\Repeater::make('slides')
                                        ->relationship()
                                        ->label(false)
                                        ->schema([
                                            Forms\Components\FileUpload::make('path')
                                                ->label('Imagem do Slide')
                                                ->image()
                                                ->required()
                                                ->directory('tenants/slides')
                                                ->maxSize(8192)
                                                ->rules(['dimensions:min_width=1440,min_height=810'])
                                                ->validationMessages(['dimensions' => 'A imagem deve ter no mínimo 1440×810px para boa qualidade no hero.'])
                                                ->columnSpanFull()
                                                ->helperText('Mínimo: 1440×810px — Ideal: 1920×1080px (Full HD). Formatos: JPG, PNG ou WebP.'),
                                            Forms\Components\TextInput::make('titulo')
                                                ->label('Título do slide')
                                                ->maxLength(150)
                                                ->placeholder('Ex: Vidraçaria em Goiânia')
                                                ->helperText('Deixe em branco para usar o título global acima'),
                                            Forms\Components\TextInput::make('subtitulo')
                                                ->label('Subtítulo do slide')
                                                ->maxLength(255)
                                                ->placeholder('Ex: Box banheiro, espelhos e esquadrias'),
                                            Forms\Components\TextInput::make('botao_label')
                                                ->label('Texto do botão CTA')
                                                ->maxLength(100)
                                                ->placeholder('Ex: Ver serviços')
                                                ->helperText('Oculto no mobile. Deixe em branco para não exibir botão'),
                                            Forms\Components\TextInput::make('botao_url')
                                                ->label('URL do botão CTA')
                                                ->maxLength(500)
                                                ->placeholder('Ex: /servicos ou https://...')
                                                ->helperText('Obrigatório se houver texto no botão acima'),
                                            Forms\Components\TextInput::make('ordem')
                                                ->label('Ordem')
                                                ->numeric()
                                                ->default(0),
                                            Forms\Components\Toggle::make('ativo')
                                                ->label('Ativo')
                                                ->default(true),
                                        ])->columns(2)
                                        ->orderColumn('ordem')
                                        ->addActionLabel('+ Adicionar Slide')
                                        ->collapsible()
                                        ->itemLabel(fn(array $state): string =>
                                            $state['titulo'] ?? ($state['path'] ? 'Slide' : 'Novo slide')
                                        ),
                                ]),

                            Forms\Components\Section::make('Destaques da Home (Features)')
                                ->description('Blocos de imagem com título e descrição exibidos na página inicial')
                                ->schema([
                                    Forms\Components\Repeater::make('features')
                                        ->relationship()
                                        ->label(false)
                                        ->schema([
                                            Forms\Components\TextInput::make('titulo')
                                                ->label('Título')
                                                ->required()
                                                ->maxLength(150),
                                            Forms\Components\Textarea::make('descricao')
                                                ->label('Descrição')
                                                ->rows(2)
                                                ->maxLength(500),
                                            Forms\Components\Select::make('tipo')
                                                ->label('Tipo de Link')
                                                ->options([
                                                    'servico' => 'Serviço (galeria)',
                                                    'pagina'  => 'Página',
                                                ])
                                                ->default('servico')
                                                ->live(),
                                            Forms\Components\TextInput::make('rota')
                                                ->label('Rota / Slug do Serviço')
                                                ->maxLength(100)
                                                ->helperText('Ex: vidros, aluminio, catalogo, sobre'),
                                            Forms\Components\FileUpload::make('imagens')
                                                ->label('Imagens do Destaque')
                                                ->image()
                                                ->multiple()
                                                ->directory('tenants/features')
                                                ->reorderable()
                                                ->maxFiles(3)
                                                ->imageResizeMode('cover')
                                                ->imageCropAspectRatio('4:3')
                                                ->imageResizeTargetWidth('800')
                                                ->imageResizeTargetHeight('600')
                                                ->maxSize(3072)
                                                ->columnSpanFull()
                                                ->helperText('Até 3 imagens. A primeira é a principal; com 3 imagens, exibe layout em grade.'),
                                            Forms\Components\TextInput::make('ordem')
                                                ->label('Ordem')
                                                ->numeric()
                                                ->default(0),
                                            Forms\Components\Toggle::make('ativo')
                                                ->label('Ativo')
                                                ->default(true),
                                        ])->columns(2)
                                        ->orderColumn('ordem')
                                        ->addActionLabel('+ Adicionar Destaque')
                                        ->collapsible()
                                        ->itemLabel(fn(array $state) => $state['titulo'] ?? 'Novo Destaque'),
                                ]),
                        ])->columns(1),

                    Forms\Components\Tabs\Tab::make('Sobre a Empresa')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('sobre_titulo')
                                ->label('Título da Seção Sobre')
                                ->maxLength(150)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('sobre_descricao')
                                ->label('Descrição / Histórico')
                                ->rows(5)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('sobre_missao')
                                ->label('Missão')
                                ->rows(3),
                            Forms\Components\Textarea::make('sobre_visao')
                                ->label('Visão')
                                ->rows(3),
                            Forms\Components\Textarea::make('sobre_valores')
                                ->label('Valores')
                                ->rows(3),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Serviços')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->schema([
                            Forms\Components\Section::make('Serviços Oferecidos')
                                ->description('Cada serviço gera uma página de galeria acessível pelo menu Serviços')
                                ->schema([
                                    Forms\Components\Repeater::make('services')
                                        ->relationship()
                                        ->label(false)
                                        ->schema([
                                            Forms\Components\TextInput::make('slug')
                                                ->label('Slug (URL)')
                                                ->required()
                                                ->maxLength(50)
                                                ->helperText('Ex: vidros, aluminio, cortina — sem espaços'),
                                            Forms\Components\TextInput::make('titulo')
                                                ->label('Título')
                                                ->required()
                                                ->maxLength(150),
                                            Forms\Components\Textarea::make('descricao')
                                                ->label('Descrição breve')
                                                ->rows(2)
                                                ->maxLength(500)
                                                ->columnSpanFull(),
                                            Forms\Components\TextInput::make('ordem')
                                                ->label('Ordem no menu')
                                                ->numeric()
                                                ->default(0),
                                            Forms\Components\Toggle::make('ativo')
                                                ->label('Ativo')
                                                ->default(true),
                                            Forms\Components\Toggle::make('mostrar_menu')
                                                ->label('Exibir no menu de navegação')
                                                ->default(true)
                                                ->helperText('Desmarcado: serviço existe como página mas não aparece no menu'),
                                        ])->columns(2)
                                        ->orderColumn('ordem')
                                        ->addActionLabel('+ Adicionar Serviço')
                                        ->collapsible()
                                        ->itemLabel(fn(array $state) => $state['titulo'] ?? 'Novo Serviço'),
                                ]),

                            Forms\Components\Section::make('Configuração do Menu de Navegação')
                                ->description('Personaliza os labels e o comportamento do menu de Serviços e do Catálogo')
                                ->schema([
                                    Forms\Components\TextInput::make('menu_servicos_label')
                                        ->label('Label do menu "Serviços"')
                                        ->default('Serviços')
                                        ->maxLength(50)
                                        ->helperText('Texto que aparece no menu de navegação para o dropdown de serviços'),
                                    Forms\Components\Toggle::make('menu_catalogo_visivel')
                                        ->label('Exibir link de Catálogo')
                                        ->default(true)
                                        ->helperText('Ativa ou oculta o link para a página de Catálogo no menu'),
                                    Forms\Components\TextInput::make('menu_catalogo_label')
                                        ->label('Label do link "Catálogo"')
                                        ->default('Catálogo de Serviços')
                                        ->maxLength(50)
                                        ->helperText('Texto exibido para o link do catálogo'),
                                    Forms\Components\Select::make('menu_catalogo_posicao')
                                        ->label('Posição do Catálogo no menu')
                                        ->options([
                                            'dropdown' => 'Dentro do dropdown de Serviços',
                                            'topo'     => 'Item separado no menu principal',
                                        ])
                                        ->default('dropdown')
                                        ->helperText('Define se o Catálogo aparece como submenu de Serviços ou como item direto no menu'),
                                ])->columns(2),
                        ])->columns(1),

                    Forms\Components\Tabs\Tab::make('Tipografia')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Forms\Components\Section::make('Fontes do Site')
                                ->description('Fontes carregadas do Google Fonts. Altere para personalizar a tipografia do tenant.')
                                ->schema([
                                    Forms\Components\TextInput::make('font_body')
                                        ->label('Fonte do Corpo (body)')
                                        ->default('Open Sans')
                                        ->maxLength(100)
                                        ->helperText('Ex: Open Sans, Lato, Roboto, Poppins — nome exato do Google Fonts'),
                                    Forms\Components\TextInput::make('font_heading')
                                        ->label('Fonte de Títulos (h1–h6 e nav)')
                                        ->default('Righteous')
                                        ->maxLength(100)
                                        ->helperText('Ex: Righteous, Montserrat, Oswald, Playfair Display'),
                                    Forms\Components\TextInput::make('font_accent')
                                        ->label('Fonte de Destaque (labels de galeria)')
                                        ->default('Josefin Sans')
                                        ->maxLength(100)
                                        ->helperText('Ex: Josefin Sans, Dancing Script, Raleway — usada nos labels flutuantes da galeria'),
                                    Forms\Components\Textarea::make('font_google_url')
                                        ->label('URL do Google Fonts (gerada automaticamente ou personalizada)')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->helperText('Cole aqui a URL do Google Fonts com todas as fontes do site. Acesse fonts.google.com, selecione as fontes e copie o link href.'),
                                ])->columns(2),
                        ])->columns(1),

                    Forms\Components\Tabs\Tab::make('Catálogo')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\Section::make('Google Drive — Listagem Dinâmica de Imagens')
                                ->description('Configure para listar imagens diretamente de uma pasta pública do Google Drive. Se não configurado, o catálogo exibe as imagens importadas localmente.')
                                ->schema([
                                    Forms\Components\TextInput::make('google_drive_api_key')
                                        ->label('API Key do Google Drive')
                                        ->maxLength(200)
                                        ->password()
                                        ->revealable()
                                        ->columnSpanFull()
                                        ->helperText('Chave de API do Google Cloud com Drive API v3 habilitada (somente leitura de arquivos públicos)'),
                                    Forms\Components\TextInput::make('google_drive_folder_id')
                                        ->label('ID da Pasta do Google Drive')
                                        ->maxLength(100)
                                        ->columnSpanFull()
                                        ->helperText('ID da pasta pública. Exemplo: 1qYHQqfFC0F8sdQzv3xVxlBax8ZzLR2rK — aparece na URL ao abrir a pasta no Drive'),
                                ])->columns(1),
                        ])->columns(1),

                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('dominio')
                    ->label('Domínio')
                    ->copyable()
                    ->color('primary'),
                Tables\Columns\ColorColumn::make('cor_primaria')
                    ->label('Cor'),
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Serviços')
                    ->counts('services')
                    ->badge(),
                Tables\Columns\IconColumn::make('ativo')
                    ->boolean()
                    ->label('Ativa'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('nome')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        Cache::forget("tenant:id:slug:{$record->slug}");
                        Cache::forget("tenant:id:domain:{$record->dominio}");
                        app(GoogleDriveService::class)->clearCache($record->slug);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit'   => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
}
