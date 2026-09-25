<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Account;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Generate no_slip otomatis
        $latest = \App\Models\Transaction::latest('id')->first();
        $nextNumber = $latest ? $latest->id + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $data['no_slip'] = "{$formattedNumber}/2026";

        return $data;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;

        // Cek jika jenis transaksi adalah setoran
        if ($transaction->jenis_transaksi === 'setoran') {
            $account = Account::find($transaction->account_id);
            
            if ($account) {
                $account->saldo += $transaction->jumlah_transaksi;
                $account->save();
            }
        }
    }
}