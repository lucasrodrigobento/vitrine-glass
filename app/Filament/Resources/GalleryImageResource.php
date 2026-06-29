<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryImageResource\Pages;
use App\Models\GalleryImage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Galeria';

    protected static ?string $modelLabel = 'Imagem';

    protected static ?string $pluralModelLabel = 'Imagens';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('tenant_slug')
                ->label('Marca')
                ->options(static::getTenantOptions())
                ->required(),
            Forms\Components\Select::make('categoria')
                ->label('Categoria')
                ->options(static::getCategoriaOptions())
                ->required(),
            Forms\Components\FileUpload::make('path')
                ->label('Imagem')
                ->image()
                ->directory('gallery')
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('4:3')
                ->imageResizeTargetWidth('1200')
                ->imageResizeTargetHeight('900')
                ->maxSize(5120)
                ->required(),
            Forms\Components\TextInput::make('titulo')
                ->label('Título')
                ->maxLength(150),
            Forms\Components\TextInput::make('ordem')
                ->label('Ordem')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('ativo')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')->label('Imagem'),
                Tables\Columns\TextColumn::make('tenant_slug')->label('Marca')->sortable(),
                Tables\Columns\TextColumn::make('categoria')->label('Categoria')->sortable(),
                Tables\Columns\TextColumn::make('titulo')->label('Título')->limit(40),
                Tables\Columns\TextColumn::make('ordem')->label('Ordem')->sortable(),
                Tables\Columns\IconColumn::make('ativo')->boolean()->label('Ativo'),
            ])
            ->defaultSort('tenant_slug')
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_slug')
                    ->label('Marca')
                    ->options(static::getTenantOptions()),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGalleryImages::route('/'),
            'create' => Pages\CreateGalleryImage::route('/create'),
            'edit'   => Pages\EditGalleryImage::route('/{record}/edit'),
        ];
    }

    private static function getTenantOptions(): array
    {
        $options = [];
        foreach (glob(config_path('tenants/*.php')) as $file) {
            $cfg = require $file;
            $options[$cfg['slug']] = $cfg['nome'];
        }
        return $options;
    }

    private static function getCategoriaOptions(): array
    {
        return [
            'vidros'   => 'Espelhos e Vidraçaria',
            'aluminio' => 'Esquadrias de Alumínio',
            'cortina'  => 'Cortinas e Acessórios',
            'box'      => 'Box Banheiro',
            'portas'   => 'Portas de Vidro',
            'geral'    => 'Geral / Portfólio',
        ];
    }
}
