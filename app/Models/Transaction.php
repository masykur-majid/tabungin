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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->no_slip)) {
                $transaction->no_slip = self::generateNoSlip();
            }
        });
    }

    public static function generateNoSlip(): string
    {
        $year = date('Y');
        $month = date('m');

       
        $count = self::whereYear('created_at', $year)
                     ->whereMonth('created_at', $month)
                     ->count();

        $nextNumber = $count + 1;
        return $nextNumber . '/' . $nextNumber;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function account(): BelongsTo
{
    return $this->belongsTo(Account::class, 'nomor_rekening', 'id');
}
}