<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Guarded(['id'])]
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;


    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'customer_id', 'id');
    }

    
    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'customer_id', 'id');
    }
}