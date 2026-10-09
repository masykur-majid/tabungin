<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Services\TransactionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Override;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    #[Override]
    public function getHeading(): string|Htmlable|null
    {
        return "Input Transaksi Baru";
    }

    #[Override]
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(TransactionService::class)->record(
                (int) $data['account_id'],
                $data['jenis_transaksi'],
                (int) $data['jumlah_transaksi'],
                auth()->id()
            );
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->color('danger')
                ->title('TRANSAKSI GAGAL!')
                ->body($e->getMessage())
                ->persistent()
                ->send();

            throw new Halt();
        }
        
    }

    public function getMaxContentWidth(): ?string
    {
        return '3xl';
    }
}
