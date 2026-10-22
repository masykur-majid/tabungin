<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $guarded = ['id'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Transaction $transaction): void {
            if (empty($transaction->no_slip)) {
                $transaction->no_slip = static::generateNoSlip();
            }
        });
    }

    public static function generateNoSlip(): string
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');

        $terakhir = static::where('no_slip', 'like', "%/{$bulan}/{$tahun}")
            ->orderByDesc('id')
            ->value('no_slip');

        $urut = $terakhir ? ((int) explode('/', $terakhir)[0]) + 1 : 1;

        return sprintf('%03d/%s/%s', $urut, $bulan, $tahun);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}