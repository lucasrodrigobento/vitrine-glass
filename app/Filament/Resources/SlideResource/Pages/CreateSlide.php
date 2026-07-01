<?php

namespace App\Filament\Resources\SlideResource\Pages;

use App\Filament\Resources\SlideResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateSlide extends CreateRecord
{
    protected static string $resource = SlideResource::class;

    protected function afterCreate(): void
    {
        Cache::forget("tenant:{$this->record->tenant->dominio}");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
