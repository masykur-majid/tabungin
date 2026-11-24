<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->adjust(
            $transaction->account_id,
            $transaction->jenis_transaksi,
            $transaction->jumlah_transaksi,
            1
        );
    }

    public function updated(Transaction $transaction): void
    {
        $wasCancelled = $transaction->getOriginal('status') === 'dibatalkan';
        $isCancelled  = $transaction->status === 'dibatalkan';

        if ($wasCancelled && $isCancelled) {
            return;
        }

        // Baru saja dibatalkan: balikkan efek transaksi ke saldo.
        if (! $wasCancelled && $isCancelled) {
            $this->adjust(
                $transaction->getOriginal('account_id'),
                $transaction->getOriginal('jenis_transaksi'),
                $transaction->getOriginal('jumlah_transaksi'),
                -1
            );
            return;
        }

        // Pembatalan dibatalkan (status kembali aktif): terapkan lagi.
        if ($wasCancelled && ! $isCancelled) {
            $this->adjust(
                $transaction->account_id,
                $transaction->jenis_transaksi,
                $transaction->jumlah_transaksi,
                1
            );
            return;
        }

        if ($transaction->wasChanged(['account_id', 'jenis_transaksi', 'jumlah_transaksi'])) {
            $this->adjust(
                $transaction->getOriginal('account_id'),
                $transaction->getOriginal('jenis_transaksi'),
                $transaction->getOriginal('jumlah_transaksi'),
                -1
            );

            $this->adjust(
                $transaction->account_id,
                $transaction->jenis_transaksi,
                $transaction->jumlah_transaksi,
                1
            );
        }
    }

    public function deleted(Transaction $transaction): void
    {
       
        if ($transaction->status === 'dibatalkan') {
            return;
        }

        $this->adjust(
            $transaction->account_id,
            $transaction->jenis_transaksi,
            $transaction->jumlah_transaksi,
            -1
        );
    }

    protected function adjust($accountId, ?string $jenis, $jumlah, int $sign): void
    {
        $account = Account::find($accountId);

        if (! $account) {
            return;
        }

        $delta = match ($jenis) {
            'setoran'   => (float) $jumlah,
            'penarikan' => -(float) $jumlah,
            default     => 0.0,
        };

        $delta *= $sign;

        if ($delta > 0) {
            $account->increment('saldo', $delta);
        } elseif ($delta < 0) {
            $account->decrement('saldo', abs($delta));
        }
    }
}