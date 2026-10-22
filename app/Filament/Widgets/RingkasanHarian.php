<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Widgets\Widget;

class RingkasanHarian extends Widget
{
    // Keduanya HARUS non-static (tanpa kata kunci 'static')
    protected string $view = 'filament.widgets.ringkasan-harian';

    protected int|string|array $columnSpan = 'full';

    // Hanya $sort yang menggunakan static
    protected static ?int $sort = -10;

    public function getRingkasanProperty(): array
    {
        return [
            'nasabah' => Customer::count(),
            'rekening_aktif' => Account::where('is_active', true)->count(),
            'transaksi_hari_ini' => Transaction::whereDate('created_at', today())->count(),
        ];
    }
}