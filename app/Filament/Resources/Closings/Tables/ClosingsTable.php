<?php

namespace App\Filament\Resources\Closings\Tables;

use App\Enums\ClosingStatus;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClosingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('total_sistem')
                    ->label('Total Uang Sistem')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_fisik')
                    ->label('Total Uang Fisik')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('selisih')
                    ->label('Selisih Kas')
                    ->numeric(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        ClosingStatus::Pending,
                        'pengajuan_tutup' => 'Pengajuan Tutup',

                        ClosingStatus::Close,
                        'tutup' => 'Tutup',

                        default => $state instanceof ClosingStatus
                            ? $state->getLabel()
                            : $state,
                    })
                    ->badge()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make()
                        ->visible(fn ($record): bool =>
                            $record->status === ClosingStatus::Pending
                            || auth()->user()?->hasRole([
                                'super_admin',
                                'supervisor',
                            ])
                        ),

                    DeleteAction::make()
                        ->visible(fn ($record): bool =>
                            $record->status === ClosingStatus::Pending
                            || auth()->user()?->hasRole([
                                'super_admin',
                                'supervisor',
                            ])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Closing')
                        ->modalDescription(
                            'Yakin ingin menghapus data closing ini?'
                        )
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
