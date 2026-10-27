<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RingkasanHarian extends Widget
{
    protected string $view = 'filament.widgets.ringkasan-harian';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public function getRingkasanProperty(): array
    {
        $hariIni = Carbon::today();

        $transaksiSukses = Transaction::whereDate('created_at', $hariIni)
            ->where('status', 'sukses');

        $totalSetoran = (clone $transaksiSukses)
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');

        $totalPenarikan = (clone $transaksiSukses)
            ->where('jenis_transaksi', 'penarikan')
            ->sum('jumlah_transaksi');

        return [
            'nasabah' => Customer::count(),
            'rekening_aktif' => Account::where('is_active', true)->count(),
            'transaksi_hari_ini' => (clone $transaksiSukses)->count(),
            'uang_terkumpul' => $totalSetoran - $totalPenarikan,
        ];
    }
}