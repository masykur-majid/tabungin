<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClosing extends CreateRecord
{
    protected static string $resource = ClosingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $selisih = (int) ($data['selisih'] ?? 0);

        if ($selisih === 0) {
            $data['status'] = 'Tutup';
        } else {
            $data['status'] = 'Selisih';
        }

        return $data;
    }
}