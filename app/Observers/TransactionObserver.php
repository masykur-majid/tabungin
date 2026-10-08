<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->updateSaldo($transaction);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->reverseSaldo($transaction);
    }

    protected function updateSaldo(Transaction $transaction): void
    {
        $account = $transaction->account;

        if (!$account) {
            return;
        }
        
        if($transaction->status != 'dibatalkan' || $transaction->status != 'batal'){
            match ($transaction->jenis_transaksi) {
                'setoran'   => $account->increment('saldo', $transaction->jumlah_transaksi),
                'penarikan' => $account->decrement('saldo', $transaction->jumlah_transaksi),
                default     => null,
            };
        }else{
            return ;
        }
        
    }

    protected function reverseSaldo(Transaction $transaction): void
    {
        $account = $transaction->account;

        if (!$account) {
            return;
        }

        match ($transaction->jenis_transaksi) {
            'setoran'   => $account->decrement('saldo', $transaction->jumlah_transaksi),
            'penarikan' => $account->increment('saldo', $transaction->jumlah_transaksi),
            default     => null,
        };
    } 
        public function updated(Transaction $transaction): void
    {
        $original = $transaction->getOriginal();
        $oldTransaction = new Transaction($original);
        $this->reverseSaldo($oldTransaction);

        $this->updateSaldo($transaction);
    }
}