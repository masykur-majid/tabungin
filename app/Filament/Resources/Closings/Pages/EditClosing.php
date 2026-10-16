<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditClosing extends EditRecord
{
    protected static string $resource = ClosingResource::class;

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        $defaultKoin = collect([
            100,
            200,
            500,
            1000,
        ])
            ->map(fn ($nominal) => [
                'koin' => $nominal,
                'banyak_koin' => 0,
                'sub_total_koin' => 0,
            ])
            ->all();

        $defaultKertas = collect([
            1000,
            2000,
            5000,
            10000,
            20000,
            50000,
            100000,
        ])
            ->map(fn ($nominal) => [
                'kertas' => $nominal,
                'banyak_kertas' => 0,
                'sub_total_kertas' => 0,
            ])
            ->all();

        $details = $this->record
            ->closingDetails()
            ->orderBy('id')
            ->get();


        foreach ($defaultKoin as $index => &$item) {
            $detail = $details->get($index);

            if ($detail) {
                $item['banyak_koin'] =
                    (int) $detail->jumlah_pecahan;

                $item['sub_total_koin'] =
                    (int) $detail->subtotal;
            }
        }

        unset($item);

        foreach ($defaultKertas as $index => &$item) {
            $detail = $details->get($index + 4);

            if ($detail) {
                $item['banyak_kertas'] =
                    (int) $detail->jumlah_pecahan;

                $item['sub_total_kertas'] =
                    (int) $detail->subtotal;
            }
        }

        unset($item);

        $totalKoin = collect($defaultKoin)
            ->sum(
                fn ($item) =>
                    (int) $item['koin']
                    * (int) $item['banyak_koin']
            );

        $totalKertas = collect($defaultKertas)
            ->sum(
                fn ($item) =>
                    (int) $item['kertas']
                    * (int) $item['banyak_kertas']
            );

        $totalFisik =
            $totalKoin + $totalKertas;

        $data['jumlah_uang_koin'] =
            $defaultKoin;

        $data['jumlah_uang_kertas'] =
            $defaultKertas;

        $data['total_uang_koin'] =
            $totalKoin;

        $data['total_uang_kertas'] =
            $totalKertas;

        $data['total_fisik'] =
            $totalFisik;

        $tanggalClosing =
            $this->record->created_at?->toDateString();

        $data['total_sistem'] =
            (int) Transaction::query()
                ->whereDate(
                    'tanggal',
                    $tanggalClosing
                )
                ->where(
                    'jenis_transaksi',
                    'setoran'
                )
                ->sum('jumlah_transaksi');

        $data['selisih'] =
            $totalFisik
            - (int) $data['total_sistem'];

        return $data;
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        $selisih = (int) (
            $data['selisih'] ?? 0
        );

        if ($selisih !== 0) {
            throw ValidationException::withMessages([
                'selisih' =>
                    'Tutup Kas tidak dapat diajukan karena masih terdapat selisih kas. Periksa kembali uang fisik.',
            ]);
        }

        $data['status'] = 'Pending';

        return $data;
    }

    protected function afterSave(): void
    {
        $this->simpanDetailUang();
    }

    private function simpanDetailUang(): void
    {
        $data = $this->form->getState();

        $this->record
            ->closingDetails()
            ->delete();

        $details = [];

        foreach (
            ($data['jumlah_uang_koin'] ?? [])
            as $item
        ) {
            $nominal = (int) (
                $item['koin'] ?? 0
            );

            $jumlah = max(
                0,
                (int) ($item['banyak_koin'] ?? 0)
            );

            $details[] = [
                'nominal_pecahan' => $nominal,
                'jumlah_pecahan' => $jumlah,
                'subtotal' =>
                    $nominal * $jumlah,
            ];
        }

        foreach (
            ($data['jumlah_uang_kertas'] ?? [])
            as $item
        ) {
            $nominal = (int) (
                $item['kertas'] ?? 0
            );

            $jumlah = max(
                0,
                (int) ($item['banyak_kertas'] ?? 0)
            );

            $details[] = [
                'nominal_pecahan' => $nominal,
                'jumlah_pecahan' => $jumlah,
                'subtotal' =>
                    $nominal * $jumlah,
            ];
        }

        $this->record
            ->closingDetails()
            ->createMany($details);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(
                    'Hapus Data Closing'
                )
                ->modalDescription(
                    'Yakin ingin menghapus data closing ini?'
                )
                ->modalSubmitActionLabel(
                    'Ya, Hapus'
                ),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Ajukan Tutup Kas');
    }
}