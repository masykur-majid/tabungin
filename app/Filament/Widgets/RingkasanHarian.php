<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RingkasanHarian extends Widget
{
    // Keduanya HARUS non-static (tanpa kata kunci 'static')
    protected string $view = 'filament.widgets.ringkasan-harian';

    protected int|string|array $columnSpan = 'full';

    // Hanya $sort yang menggunakan static
    protected static ?int $sort = -10;

    public function getRingkasanProperty(): array
    {
        $totalSetoran = Transaction::whereDate('created_at', Carbon::today())
            ->where('status', 'sukses')
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');

        $totalPenarikan = Transaction::whereDate('created_at', Carbon::today())
            ->where('status', 'sukses')
            ->where('jenis_transaksi', 'penarikan')
            ->sum('jumlah_transaksi');

        return [
            'nasabah' => Customer::count(),
            'rekening_aktif' => Account::where('is_active', true)->count(),
            'transaksi_hari_ini' => Transaction::whereDate('created_at', today())->count(),
        ];
    }
}