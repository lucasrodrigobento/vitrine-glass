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
                                ->helperText('Cor principal da marca — navbar, botões, destaques'),
                            Forms\Components\ColorPicker::make('cor_secundaria')
                                ->label('Cor Secundária')
                                ->required()
                                ->default('#9e2016')
                                ->helperText('Cor complementar — gradientes, hovers'),
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
                                ->label('ID Google Ads (Tag Manager)')
                                ->maxLength(50)
                                ->helperText('Ex: AW-666035862'),
                            Forms\Components\Textarea::make('google_maps_embed')
                                ->label('Embed Google Maps')
                                ->rows(3)
                                ->columnSpanFull()
                                ->helperText('Cole aqui o src do iframe gerado pelo Google Maps → Compartilhar → Incorporar'),
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
                                ->schema([
                                    Forms\Components\TextInput::make('hero_titulo')
                                        ->label('Título do Hero')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('hero_subtitulo')
                                        ->label('Subtítulo do Hero')
                                        ->maxLength(255),
                                ])->columns(2),

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
                                        ])->columns(2)
                                        ->orderColumn('ordem')
                                        ->addActionLabel('+ Adicionar Serviço')
                                        ->collapsible()
                                        ->itemLabel(fn(array $state) => $state['titulo'] ?? 'Novo Serviço'),
                                ]),
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
