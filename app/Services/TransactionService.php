<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class TransactionService
{

    //function for record the data to database
    public function record(
        int $accountId,
        string $type,
        int $amount,
        ?int $userId = null,
    ): Transaction
    {

        if ($amount <= 0){
            throw new InvalidArgumentException('Jumlah uang yang disetor/ditarik harus lebih dari Rp0');
        }

        if (! in_array($type, ['setoran', 'penarikan'], true)){
            throw new InvalidArgumentException("Jenis transaksi tidak dikenal: {$type}");
        }

        return DB::transaction(function() use ($accountId, $type, $amount, $userId){
            //get info about the account
            $account = Account::lockForUpdate()->findOrFail($accountId);

            if(! $account->is_active){
                throw new RuntimeException('Rekening tidak aktif, transaksi tidak dapat dilakukan');
            }

            //stop the transaction when the current balance is not enough for doing withdrawal
            if($type === 'penarikan' && $account->saldo < $amount){
                throw new RuntimeException('Saldo tidak cukup');
            }

            //doing the calculation for balance
            $type === 'setoran' ? $account->increment('saldo', $amount) : $account->decrement('saldo', $amount);

            return $account->transactions()->create([
                'jenis_transaksi' => $type,
                'jumlah_transaksi' => $amount,
                'status' => TransactionStatus::Success,
                'no_slip' => $this->createReceiptNumber($type),
                'user_id' => $userId,
            ]);
        });
    }   

    //function for requesting cancellation
    public function requestCancellation(int $transactionId, int $requesterId, string $reason): Transaction
    {
        if(trim($reason) === ''){
            throw new InvalidArgumentException('Alasan pembatalan wajib diisi.');
        }

        $this->ensureAuthorized($requesterId, 'RequestCancellation:Transaction');

        return DB::transaction(function() use ($transactionId, $requesterId, $reason){
            $trx = Transaction::lockForUpdate()->findOrFail($transactionId);

            if($trx->status !== TransactionStatus::Success){
                throw new RuntimeException('Hanya transaksi dengan status berhasil yang dapat diajukan pembatalan.');
            }

            $trx->update([
                'status' => TransactionStatus::PendingCancellation,
                'diajukan_batal_oleh' => $requesterId,
                'tanggal_pengajuan_batal' => now(),
                'alasan_batal' => $reason,
            ]);

            return $trx;

        });
    }
    //function for approving cancellation
    public function approveCancellation(int $transactionId, int $approverId): Transaction
    {
        $this->ensureAuthorized($approverId, 'ApproveCancellation:Transaction');

        return DB::transaction(function() use ($transactionId, $approverId){
            $trx = Transaction::lockForUpdate()->findOrFail($transactionId);

            if($trx->status !== TransactionStatus::PendingCancellation){
                throw new RuntimeException('Tidak ada pengajuan pembatalan untuk transaksi ini.');
            }

            if($trx->diajukan_batal_oleh === $approverId){
                throw new RuntimeException('Pengaju tidak boleh menyetujui pengajuannya sendiri.');
            }

            $account = Account::lockForUpdate()->findOrFail($trx->account_id);

            if($trx->jenis_transaksi === 'setoran'){
                if($account->saldo < $trx->jumlah_transaksi){
                    throw new RuntimeException('saldo tidak cukup untuk membatalkan setoran ini. Tolak atau selesaikan dulu.');
                }
                $account->decrement('saldo', $trx->jumlah_transaksi);
            }else{
                $account->increment('saldo', $trx->jumlah_transaksi);
            }

            $trx->update([
                'status' => TransactionStatus::Cancelled,
                'dibatalkan_oleh' => $approverId,
                'dibatalkan_pada' => now(),
            ]);

            return $trx;
        });

    }

    private function ensureAuthorized(int $userId, string $permission)
    {
        if(! User::findOrFail($userId)->can($permission)){
            throw new RuntimeException('Anda tidak berhak melakukan aksi ini.');
        }
    }

    private function createReceiptNumber(string $transactionType){
        $noSlip = DB::transaction(function() use ($transactionType){

            $transactionType == 'setoran' ? $type = 'DP' : $type = 'WD'; 

            $bulanRomawi = [
                1 => "I",
                2 => "II",
                3 => "III",
                4 => "IV",
                5 => "V",
                6 => "VI",
                7 => "VII",
                8 => "VII",
                9 => "IX",
                10 => "X",
                11 => "XI",
                12 => "XII"
            ];

            $bulanSaatIni = now()->month;
            $tahunSaatIni = now()->year;


            $transaksiTerakhir = Transaction::whereYear('created_at', $tahunSaatIni)
                                    ->whereMonth('created_at', $bulanSaatIni)
                                    ->lockForUpdate()
                                    ->latest('id')
                                    ->first();
            
            if($transaksiTerakhir && $transaksiTerakhir->no_slip){
                $split = explode('/', $transaksiTerakhir->no_slip);
                $nomorUrutTerakhir = (int) $split[0];
                $nomorUrutBaru = $nomorUrutTerakhir + 1;
            }else{
                $nomorUrutBaru = 1;
            }


            return 'TRX-'.Str::padLeft($nomorUrutBaru, 3, '0').'/'.$type.'/'.$bulanRomawi[$bulanSaatIni].'/'.$tahunSaatIni;
        });
        
        return $noSlip;
    }
}
