<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Enums\Enums\ClosingStatus;
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
                    && auth()->user()?->hasRole('supervisor')
                )
                ->requiresConfirmation()
                ->modalHeading('Setujui Penutupan Kas')
                ->modalDescription(
                    'Setujui penutupan kas ini? Status akan berubah menjadi Tutup.'
                )
                ->modalSubmitActionLabel('Ya, Setujui Penutupan')
                ->action(function (): void {
                    abort_unless(
                        auth()->user()?->hasRole('supervisor'),
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
                        ]);
                    });

                    $this->record->refresh();
                }),

            EditAction::make()
                ->visible(fn (): bool =>
                    $this->record->status === ClosingStatus::Pending
                ),

            DeleteAction::make()
                ->visible(fn (): bool =>
                    $this->record->status === ClosingStatus::Pending
                )
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Closing')
                ->modalDescription(
                    'Yakin ingin menghapus data closing ini?'
                )
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }
}