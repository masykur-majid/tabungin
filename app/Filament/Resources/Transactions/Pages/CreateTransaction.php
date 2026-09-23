<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    #[Override]
    public function mutateFormDataBeforeCreate(array $data): array
    {
        return [];
    }
}
