<?php

namespace App\Filament\Resources\TransactionCorrections\Pages;

use App\Filament\Resources\TransactionCorrections\TransactionCorrectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransactionCorrection extends ViewRecord
{
    protected static string $resource = TransactionCorrectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
