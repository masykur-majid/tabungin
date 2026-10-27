<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use App\Enums\Enums\ClosingStatus;
use App\Models\Closing;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateClosing extends CreateRecord
{
    protected static string $resource = ClosingResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Ajukan Tutup Kas');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Closing::query()->whereDate('created_at', today())->exists()) {
            Notification::make()
                ->danger()
                ->title('Tutup Kas Sudah Dibuat')
                ->body('Tutup kas hanya boleh dibuat satu kali sehari.')
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'created_at' => 'Tutup kas hari ini sudah pernah dibuat.',
            ]);
        }

        $tanggal = today()->toDateString();

        $totalSistem = (int) Transaction::query()
            ->whereDate('tanggal', $tanggal)
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
        );

        $data['user_id'] = auth()->id();
        $data['total_sistem'] = $totalSistem;
        $data['total_fisik'] = $totalFisik;
        $data['selisih'] = $selisih;
        $data['status'] = ClosingStatus::Pending;
        $data['is_approved'] = false;

        return $data;
    }

    protected function afterCreate(): void
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
}
