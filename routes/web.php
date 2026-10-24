<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/bukti-transaksi/{transaction}', function (Transaction $transaction) {
    $transaction->load(['account.customer', 'user']);

    return view('bukti-transaksi', compact('transaction'));
})->middleware('auth')->name('transaksi.bukti');