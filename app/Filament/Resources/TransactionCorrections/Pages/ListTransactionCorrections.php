<?php

namespace App\Filament\Resources\TransactionCorrections\Pages;

use App\Filament\Resources\TransactionCorrections\TransactionCorrectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactionCorrections extends ListRecords
{
    protected static string $resource = TransactionCorrectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
