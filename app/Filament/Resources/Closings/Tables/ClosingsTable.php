<?php

namespace App\Filament\Resources\Closings\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                
                // TANGGAL CLOSING DIBUAT
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('total_sistem')
                    ->label('Total Sistem')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_fisik')
                    ->label('Total Fisik')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                // WAKTU TERAKHIR DATA DIUBAH
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make(),

                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Closing')
                        ->modalDescription(
                            'Yakin ingin menghapus data ini?'
                        )
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}