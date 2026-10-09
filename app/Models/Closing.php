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
    use HasFactory;

    /*
     * Relasi ke detail uang.
     */
    public function closingDetails(): HasMany
    {
        return $this->hasMany(
            ClosingDetail::class,
            'closing_id',
            'id'
        );
    }

    /*
     * Relasi ke user/petugas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    /*
     * Kalau Closing dihapus,
     * detail uangnya ikut dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(
            function (Closing $closing) {

                $closing
                    ->closingDetails()
                    ->delete();
            }
        );
    }
}