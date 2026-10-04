<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Override;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $noSlip = DB::transaction(function(){
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

            return Str::padLeft($nomorUrutBaru, 3, '0').'/'.$bulanRomawi[$bulanSaatIni].'/'.$tahunSaatIni;
        });
        $data['no_slip'] = $noSlip;
        return $data;
    }

    public function getMaxContentWidth(): ?string
    {
        return '3xl';
    }
}
