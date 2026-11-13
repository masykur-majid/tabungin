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

    $transactions = Transaction::query()
        ->with(['account.customer'])
        ->whereDate('created_at', $tanggal)
        ->where('jenis_transaksi', 'setoran')
        ->where('status', 'sukses')
        ->orderBy('created_at')
        ->get();

    return view('laporan-closing', compact(
        'closing',
        'transactions'
    ));
})->middleware('auth')->name('closing.cetak');
