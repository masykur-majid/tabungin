<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class SaldoOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public $selectedAccountId = null;

    #[On('update-selected-account')]
    public function updateAccount($accountId = null)
    {
        $this->selectedAccountId = $accountId;
    }

    protected function getStats(): array
    {
        $account = null;

        if ($this->selectedAccountId) {
            $account = Account::find($this->selectedAccountId);
        }

        $saldo = $account?->saldo ?? 0;

        return [
            Stat::make('Saldo Rekening Utama', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
        ];
    }
}