<?php

namespace App\Enums\Enums;
 
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
enum ClosingStatus: string implements HasLabel, HasColor
{
    case Pending    = 'pengajuan_tutup';
    case Close      = 'tutup';


    public function getLabel(): string
    {
        return match ($this) {
            self::Pending   => 'pengajuan tutup',            
            self::Close     => 'tutup',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending  => 'warning', 
            self::Close    => 'success',
        };
    }
}
