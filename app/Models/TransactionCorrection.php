<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCorrection extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'alasan',
        'status',
        'catatan_admin',
        'approved_by',
        'approved_at',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
