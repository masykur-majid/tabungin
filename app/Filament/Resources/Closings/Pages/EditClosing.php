<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClosing extends EditRecord
{
    protected static string $resource = ClosingResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $selisih = (int) ($data['selisih'] ?? 0);

        if ($selisih === 0) {
            $data['status'] = 'Tutup';
        } else {
            $data['status'] = 'Selisih';
        }

        return $data;
    }

    protected function afterFill(): void
    {
        $this->form->fill([
            ...$this->form->getState(),
            'status' => 'Buka',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}