<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

        public function mount(int | string $record): void
    {
        parent::mount($record);

        if (in_array($this->record->status, ['sukses', 'dibatalkan'])) {
            $this->redirect(TransactionResource::getUrl('view', ['record' => $this->record]));
        }
    }

}
