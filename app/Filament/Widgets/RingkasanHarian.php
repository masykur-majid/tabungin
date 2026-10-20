<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Widgets\Widget;

class RingkasanHarian extends Widget
{
    protected static string $view = 'filament.widgets.ringkasan-harian';

    protected static ?int $sort = -10;

    // Pastikan menggunakan kata kunci 'static'
    protected static int|string|array $columnSpan = 'full';

    public function getRingkasanProperty(): array
    {
        return [
            'nasabah' => Customer::count(),
            'rekening_aktif' => Account::where('is_active', true)->count(),
            'transaksi_hari_ini' => Transaction::whereDate('created_at', today())->count(),
        ];
    }
}