<?php

namespace App\Enums;
 
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
enum TransactionStatus: string implements HasLabel, HasColor
{

    case Success                = 'sukses';
    case PendingCancellation    = 'pengajuan_pembatalan';
    case Cancelled              = 'batal';


    public function getLabel(): string
    {
        return match ($this) {
            self::Success               => 'Sukses',
            self::PendingCancellation   => 'Pengajuan pembatalan',            
            self::Cancelled             => 'Batal',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Success              => 'success',
            self::PendingCancellation  => 'warning',
            self::Cancelled            => 'danger', 
        };
    }
}
