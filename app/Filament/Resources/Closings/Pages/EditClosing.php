<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditClosing extends EditRecord
{
    protected static string $resource = ClosingResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pecahanKoin = [100, 200, 500, 1000];
        $pecahanKertas = [1000, 2000, 5000, 10000, 20000, 50000, 100000];

        $defaultKoin = collect($pecahanKoin)->map(fn ($nominal) => [
            'koin' => $nominal,
            'banyak_koin' => 0,
            'sub_total_koin' => 0,
        ])->all();

        $defaultKertas = collect($pecahanKertas)->map(fn ($nominal) => [
            'kertas' => $nominal,
            'banyak_kertas' => 0,
            'sub_total_kertas' => 0,
        ])->all();

        $details = $this->record->closingDetails()->orderBy('id')->get();

        foreach ($details as $index => $detail) {
            $jenis = $detail->jenis;

            // Kompatibilitas untuk detail lama yang belum memiliki jenis.
            if (! $jenis) {
                $jenis = $index < count($pecahanKoin) ? 'koin' : 'kertas';
            }

            if ($jenis === 'koin') {
                $posisi = array_search((int) $detail->nominal_pecahan, $pecahanKoin, true);

                if ($posisi !== false) {
                    $defaultKoin[$posisi]['banyak_koin'] = (int) $detail->jumlah_pecahan;
                    $defaultKoin[$posisi]['sub_total_koin'] = (int) $detail->subtotal;
                }
            }

            if ($jenis === 'kertas') {
                $posisi = array_search((int) $detail->nominal_pecahan, $pecahanKertas, true);

                if ($posisi !== false) {
                    $defaultKertas[$posisi]['banyak_kertas'] = (int) $detail->jumlah_pecahan;
                    $defaultKertas[$posisi]['sub_total_kertas'] = (int) $detail->subtotal;
                }
            }
        }

        $totalKoin = collect($defaultKoin)->sum(
            fn ($item) => $item['koin'] * $item['banyak_koin']
        );

        $totalKertas = collect($defaultKertas)->sum(
            fn ($item) => $item['kertas'] * $item['banyak_kertas']
        );

        $totalSistem = (int) Transaction::query()
            ->whereDate('tanggal', $this->record->created_at->toDateString())
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');

        $data['jumlah_uang_koin'] = $defaultKoin;
        $data['jumlah_uang_kertas'] = $defaultKertas;
        $data['total_uang_koin'] = $totalKoin;
        $data['total_uang_kertas'] = $totalKertas;
        $data['total_fisik'] = $totalKoin + $totalKertas;
        $data['total_sistem'] = $totalSistem;
        $data['selisih'] = $data['total_fisik'] - $totalSistem;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $totalSistem = (int) Transaction::query()
            ->whereDate('tanggal', $this->record->created_at->toDateString())
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');

        $totalFisik = (int) ($data['total_fisik'] ?? 0);
        $selisih = $totalFisik - $totalSistem;

        if ($selisih !== 0) {
            throw ValidationException::withMessages([
                'selisih' => 'Tutup kas tidak dapat diajukan karena terdapat selisih kas.',
            ]);
        }

        unset(
            $data['jumlah_uang_koin'],
            $data['jumlah_uang_kertas'],
            $data['total_uang_koin'],
            $data['total_uang_kertas'],
            $data['peringatan_kas'],
            $data['created_at'],
            $data['updated_at'],
            $data['user_id'],
        );

        $data['total_sistem'] = $totalSistem;
        $data['total_fisik'] = $totalFisik;
        $data['selisih'] = $selisih;
        $data['status'] = 'pengajuan_tutup';
        $data['is_approved'] = false;

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data): void {
            $this->record->closingDetails()->delete();

            $details = [];

            foreach (($data['jumlah_uang_koin'] ?? []) as $item) {
                $nominal = (int) ($item['koin'] ?? 0);
                $jumlah = max(0, (int) ($item['banyak_koin'] ?? 0));

                $details[] = [
                    'jenis' => 'koin',
                    'nominal_pecahan' => $nominal,
                    'jumlah_pecahan' => $jumlah,
                    'subtotal' => $nominal * $jumlah,
                ];
            }

            foreach (($data['jumlah_uang_kertas'] ?? []) as $item) {
                $nominal = (int) ($item['kertas'] ?? 0);
                $jumlah = max(0, (int) ($item['banyak_kertas'] ?? 0));

                $details[] = [
                    'jenis' => 'kertas',
                    'nominal_pecahan' => $nominal,
                    'jumlah_pecahan' => $jumlah,
                    'subtotal' => $nominal * $jumlah,
                ];
            }

            $this->record->closingDetails()->createMany($details);
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Closing')
                ->modalDescription('Yakin ingin menghapus data closing ini?')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Ajukan Tutup Kas');
    }
}
