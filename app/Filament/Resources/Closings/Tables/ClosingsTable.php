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
    public static function configure(
        Table $table
    ): Table {

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
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
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
                        ->modalHeading(
                            'Hapus Data Closing'
                        )
                        ->modalDescription(
                            'Yakin ingin menghapus data closing ini?'
                        )
                        ->modalSubmitActionLabel(
                            'Ya, Hapus'
                        ),

                ]),
            ])

            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}