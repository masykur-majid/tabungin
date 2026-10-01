<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionCorrection;
use Illuminate\Http\Request;

class TransactionCorrectionController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        TransactionCorrection::create([
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Pengajuan koreksi berhasil dikirim.'
        );
    }
}