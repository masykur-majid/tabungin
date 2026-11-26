<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Enums\ClosingStatus;
use App\Filament\Resources\Closings\ClosingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewClosing extends ViewRecord
{
    protected static string $resource = ClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('setujuiPenutupan')
                ->label('Setujui Penutupan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool =>
                    $this->record->status === ClosingStatus::Pending
                    && auth()->user()?->hasRole([
                        'supervisor',
                        'super_admin',
                    ])
                )
                ->requiresConfirmation()
                ->modalHeading('Setujui Penutupan Kas')
                ->modalDescription(
                    'Setujui penutupan kas ini? Status akan berubah menjadi Tutup.'
                )
                ->modalSubmitActionLabel('Ya, Setujui Penutupan')
                ->action(function (): void {
                    abort_unless(
                        auth()->user()?->hasRole([
                            'supervisor',
                            'super_admin',
                        ]),
                        403
                    );

                    DB::transaction(function (): void {
                        $closing = $this->record
                            ->newQuery()
                            ->lockForUpdate()
                            ->findOrFail($this->record->getKey());

                        abort_unless(
                            $closing->status === ClosingStatus::Pending,
                            403
                        );

                        $closing->update([
                            'status' => ClosingStatus::Close,
                            'is_approved' => true,
                            'approver' => auth()->id(),
                        ]);
                    });

                    $this->record->refresh();
                }),

            Action::make('cetakLaporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->visible(fn (): bool =>
                    $this->record->status === ClosingStatus::Close
                    && $this->record->is_approved
                )
                ->url(fn () => route('closing.cetak', $this->record))
                ->openUrlInNewTab(),

            EditAction::make()
                ->visible(fn (): bool =>
                    ! $this->record->is_approved
                    && $this->record->status !== ClosingStatus::Close
                ),

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Closing')
                ->modalDescription(
                    'Yakin ingin menghapus data closing ini?'
                )
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }
}
