<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use App\Models\Closing;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateClosing extends CreateRecord
{
    protected static string $resource = ClosingResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Ajukan Tutup Kas');
    }

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
  
        $sudahAda = Closing::query()
            ->whereDate('created_at', today())
            ->exists();

        if ($sudahAda) {
            Notification::make()
                ->danger()
                ->title('Tutup Kas Sudah Dibuat')
                ->body(
                    'Tutup Kas hanya boleh dibuat satu kali sehari. ' .
                    'Data hari ini sudah tersedia.'
                )
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'created_at' =>
                    'Tutup Kas untuk hari ini sudah pernah dibuat.',
            ]);
        }

        $data['user_id'] = auth()->id();

        $data['total_sistem'] = (int) Transaction::query()
            ->whereDate('tanggal', today())
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');

        $totalFisik = (int) ($data['total_fisik'] ?? 0);

        $data['selisih'] =
            $totalFisik - $data['total_sistem'];

        if ((int) $data['selisih'] !== 0) {
            throw ValidationException::withMessages([
                'selisih' =>
                    'Tutup Kas tidak dapat diajukan karena masih terdapat selisih kas. Periksa kembali uang fisik.',
            ]);
        }

        $data['status'] = 'Pending';

        return $data;
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
