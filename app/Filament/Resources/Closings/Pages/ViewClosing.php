<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

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

                ->visible(function (): bool {
                    return
                        $this->record->status === 'Pending'
                        && auth()->user()?->hasRole('supervisor');
                })

                ->requiresConfirmation()

                ->modalHeading(
                    'Setujui Penutupan Kas'
                )

                ->modalDescription(
                    'Apakah Anda yakin ingin menyetujui penutupan kas ini? Setelah disetujui, status akan menjadi Tutup.'
                )

                ->modalSubmitActionLabel(
                    'Ya, Setujui Penutupan'
                )

                ->action(function (): void {

                    abort_unless(
                        auth()->user()?->hasRole('supervisor'),
                        403
                    );

                    abort_unless(
                        $this->record->status === 'Pending',
                        403
                    );

                    $this->record->update([
                        'status' => 'Tutup',
                    ]);

                    $this->record->refresh();
                }),

            EditAction::make(),

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
}