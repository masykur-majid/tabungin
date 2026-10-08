<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum TransactionStatus: string implements HasLabel, HasColor
{
    case Success           = 'berhasil';
    case PendingCancellation = 'menunggu_pembatalan';
    case Cancelled         = 'dibatalkan';

    #[Override]
    public function getLabel(): string|Htmlable|null
    {
        return match($this){
            self::Success => 'Berhasil',
            self::PendingCancellation => 'Menunggu Persetujuan Batal',
            self::Cancelled => 'Dibatalkan',
        };
    }

    #[Override]
    public function getColor(): string|array|null
    {
        return match($this){
            self::Success => Color::Green,
            self::PendingCancellation => Color::Amber,
            self::Cancelled => Color::Rose,
        };
    }

    // public function affectingBalance(): bool
    // {
    //     return $this !== self::Cancelled;
    // }

    // public function notAffectingBalance(): array
    // {
    //     return array_values(array_map(
    //         fn (self $s) => $s->value,
    //         array_filter(self::cases(), fn(self $s) => $s->affectingBalance())
    //     ));
    // }
}
