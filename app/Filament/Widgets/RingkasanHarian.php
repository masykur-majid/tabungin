<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RingkasanHarian extends Widget
{
    protected static ?int $sort = -2 ;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.ringkasan-harian';

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
            'rekening_aktif' => Account::count(),
            'transaksi_hari_ini' => Transaction::whereDate('created_at', Carbon::today())->count(),
            'uang_terkumpul_hari_ini' => $totalSetoran - $totalPenarikan,
        ];
    }
}