<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlideResource\Pages;
use App\Models\Tenant;
use App\Models\TenantSlide;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class SlideResource extends Resource
{
    protected static ?string $model = TenantSlide::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Slides (Carrossel)';

    protected static ?string $modelLabel = 'Slide';

    protected static ?string $pluralModelLabel = 'Slides';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('tenant_id')
                ->label('Empresa')
                ->options(Tenant::where('ativo', true)->pluck('nome', 'id'))
                ->required()
                ->searchable(),
            Forms\Components\FileUpload::make('path')
                ->label('Imagem do Slide')
                ->image()
                ->directory('tenants/slides')
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('16:6')
                ->imageResizeTargetWidth('1920')
                ->imageResizeTargetHeight('720')
                ->maxSize(5120)
                ->required()
                ->columnSpanFull()
                ->helperText('Proporção 16:6 recomendada (ex: 1920×720px). Máx 5MB.'),
            Forms\Components\TextInput::make('legenda')
                ->label('Legenda (opcional)')
                ->maxLength(255)
                ->columnSpanFull(),
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
                Tables\Columns\ImageColumn::make('path')
                    ->label('Slide')
                    ->width(200)
                    ->height(75),
                Tables\Columns\TextColumn::make('tenant.nome')
                    ->label('Empresa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('legenda')
                    ->label('Legenda')
                    ->limit(40),
                Tables\Columns\TextColumn::make('ordem')
                    ->label('Ordem')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ativo')
                    ->boolean()
                    ->label('Ativo'),
            ])
            ->defaultSort('tenant_id')
            ->reorderable('ordem')
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Empresa')
                    ->options(Tenant::where('ativo', true)->pluck('nome', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn($record) => Cache::forget("tenant:{$record->tenant->dominio}")),
                Tables\Actions\DeleteAction::make()
                    ->after(fn($record) => Cache::forget("tenant:{$record->tenant->dominio}")),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSlides::route('/'),
            'create' => Pages\CreateSlide::route('/create'),
            'edit'   => Pages\EditSlide::route('/{record}/edit'),
        ];
    }
}
