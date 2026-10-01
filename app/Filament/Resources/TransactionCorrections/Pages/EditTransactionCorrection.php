<?php

namespace App\Filament\Resources\TransactionCorrections\Pages;

use App\Filament\Resources\TransactionCorrections\TransactionCorrectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTransactionCorrection extends EditRecord
{
    protected static string $resource = TransactionCorrectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
