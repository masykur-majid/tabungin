<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionCorrectionController;

Route::post(
    '/transactions/{transaction}/correction',
    [TransactionCorrectionController::class, 'store']
)->name('transactions.correction.store');