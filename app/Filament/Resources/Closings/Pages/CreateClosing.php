<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClosing extends CreateRecord
{
    protected static string $resource = ClosingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $selisih = (int) ($data['selisih'] ?? 0);

        if ($selisih === 0) {
            $data['status'] = 'Tutup';
        } else {
            $data['status'] = 'Selisih';
        }

        return $data;
    }

    protected function beforeCreate(): void
    {
        /*
         * Karena created_at belum ada sebelum record dibuat,
         * total sistem dihitung berdasarkan tanggal hari ini.
         */
        $this->data['total_sistem'] =
            \App\Models\Transaction::query()
                ->whereDate('tanggal', today())
                ->where('jenis_transaksi', 'setoran')
                ->sum('jumlah_transaksi');
    }

    protected function afterCreate(): void
    {
        $this->simpanDetailUang();
    }

    private function simpanDetailUang(): void
    {
        $data = $this->form->getState();

        $this->record->closingDetails()->delete();

        $details = [];

        // =========================
        // SIMPAN UANG KOIN
        // =========================

        foreach (($data['jumlah_uang_koin'] ?? []) as $item) {
            $nominal = (int) ($item['koin'] ?? 0);
            $jumlah = max(
                0,
                (int) ($item['banyak_koin'] ?? 0)
            );

            $details[] = [
                'nominal_pecahan' => $nominal,
                'jumlah_pecahan' => $jumlah,
                'subtotal' => $nominal * $jumlah,
            ];
        }

        // =========================
        // SIMPAN UANG KERTAS
        // =========================

        foreach (($data['jumlah_uang_kertas'] ?? []) as $item) {
            $nominal = (int) ($item['kertas'] ?? 0);
            $jumlah = max(
                0,
                (int) ($item['banyak_kertas'] ?? 0)
            );

            $details[] = [
                'nominal_pecahan' => $nominal,
                'jumlah_pecahan' => $jumlah,
                'subtotal' => $nominal * $jumlah,
            ];
        }

        $this->record->closingDetails()->createMany($details);
    }
}