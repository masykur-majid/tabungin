<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Exception;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;
use RuntimeException;

#[Guarded('id')]

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    #[Override]
    protected static function booted()
    {
        static::deleting(fn() => throw new RuntimeException('transaksi tidak boleh dihapus'));

        static::updating(function (Transaction $trx){
            $allowed = [
                'status', 'alasan_batal', 'alasan_tolak',
                'diajukan_batal_oleh', 'tanggal_pengajuan_batal',
                'dibatalkan_oleh', 'tanggal_dibatalkan',
                'closing_id', 'updated_at',
            ];

            $restrict = array_diff(array_keys($trx->getDirty()), $allowed);
            $sudahBatal = $trx->getOriginal('status') === TransactionStatus::Cancelled;

            if($restrict || $sudahBatal){
                throw new RuntimeException('Transaksi tidak boleh diubah.');
            }
        });

        return parent::booted();
    }

    #[Override]
    protected function casts()
    {
        return [
            'status' => TransactionStatus::class,
            'tanggal_pengajuan_batal' => 'datetime',
            'tanggal_dibatalkan' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

     // parent::boot();

        // static::creating(function ($transaction) {
        //     if (empty($transaction->no_slip)) {
        //         $transaction->no_slip = self::generateNoSlip();
        //     }
        // });

    // public static function generateNoSlip(): string
    // {
    //     $lastNumber = self::count() + 1;
    //     return $lastNumber . '/' . $lastNumber;
    // }

}