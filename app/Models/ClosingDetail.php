<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded('id')]
class ClosingDetail extends Model
{
    use HasFactory;

    public function closing(): BelongsTo
    {
        return $this->belongsTo(Closing::class);
    }
}
