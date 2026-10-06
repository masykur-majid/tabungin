<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SaldoOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $saldo = Account::sum('saldo');

        return [
            Stat::make(
                'Saldo Rekening',
                'Rp ' . number_format($saldo, 0, ',', '.')
            )
                ->description('Total saldo rekening')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
