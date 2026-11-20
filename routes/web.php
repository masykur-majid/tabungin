<?php

use App\Models\Closing;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/bukti-transaksi/{transaction}', function (Transaction $transaction) {
    $transaction->load(['account.customer', 'user']);

    return view('bukti-transaksi', compact('transaction'));
})->middleware('auth')->name('transaksi.bukti');

Route::get('/laporan-closing/{closing}', function (Closing $closing) {
    abort_unless(
        $closing->status === \App\Enums\ClosingStatus::Close
        && $closing->is_approved,
        403
    );

    abort_unless(
        auth()->user()?->hasRole(['super_admin', 'supervisor'])
        || $closing->user_id === auth()->id(),
        403
    );

    $closing->load([
        'user',
        'approverUser',
        'closingDetails',
    ]);

    $tanggal = $closing->created_at->toDateString();

    $transaksiSetoran = Transaction::query()
        ->whereDate('created_at', $tanggal)
        ->where('jenis_transaksi', 'setoran')
        ->where('status', 'sukses');

    $transaksiPenarikan = Transaction::query()
        ->whereDate('created_at', $tanggal)
        ->where('jenis_transaksi', 'penarikan')
        ->where('status', 'sukses');

    $transaksiVoid = Transaction::query()
        ->whereDate('created_at', $tanggal)
        ->where('status', 'batal');

    $rekap = [
        'jumlah_setoran' => (clone $transaksiSetoran)->count(),
        'jumlah_penarikan' => (clone $transaksiPenarikan)->count(),

        'total_setoran' => (clone $transaksiSetoran)
            ->sum('jumlah_transaksi'),

        'total_penarikan' => (clone $transaksiPenarikan)
            ->sum('jumlah_transaksi'),

        'jumlah_void' => (clone $transaksiVoid)->count(),

        'total_void' => (clone $transaksiVoid)
            ->sum('jumlah_transaksi'),
    ];

    return view('laporan-closing', compact(
        'closing',
        'rekap'
    ));
})->middleware('auth')->name('closing.cetak');
