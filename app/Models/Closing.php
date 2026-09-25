<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class Closing extends Model
{
    /** @use HasFactory<\Database\Factories\ClosingFactory> */
    use HasFactory;

    public function closingDetails(): HasMany
    {
        return $this->hasMany(
            ClosingDetail::class,
            'closing_id',
            'id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    protected static function booted(): void
    {
        static::deleting(function (Closing $closing) {
            $closing->closingDetails()->delete();
        });
    }
}