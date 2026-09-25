<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]

class ClosingDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ClosingDetailFactory> */
    use HasFactory;
}
