<?php

namespace App\Filament\Resources\GalleryImageResource\Pages;

use App\Filament\Resources\GalleryImageResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryImage extends CreateRecord
{
    protected static string $resource = GalleryImageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['tenant_id']) && empty($data['tenant_slug'])) {
            $data['tenant_slug'] = Tenant::find($data['tenant_id'])?->slug;
        }
        return $data;
    }
}
