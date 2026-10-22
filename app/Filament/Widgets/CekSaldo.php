<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\Widget;

class CekSaldo extends Widget
{
    protected string $view = 'filament.widgets.cek-saldo';

    public string $search = '';

    public function getAccountsProperty()
    {
        if (blank($this->search)) {
            return collect();
        }

        return Account::with('customer')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('nomor_rekening', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($query) {
                        $query->where('nama', 'like', '%' . $this->search . '%');
                    });
            })
            ->get();
    }
}