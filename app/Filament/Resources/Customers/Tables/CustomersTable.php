<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('NISN')
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                    'Laki-laki' => 'info',    
                    'Perempuan' => 'danger',
                    default => 'gray',
                 })
                 ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('asal_sekolah')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                    'SMKS 1 PARAHYANGAN' => 'gray', 
                    'SMP PARAHYANGAN'    => 'success', 
                    default => 'gray',
                 }),
                TextColumn::make('alamat_rumah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
           
          ->actions([
                ViewAction::make()
                    ->hiddenLabel()
                    ->tooltip('View')
                    ->slideOver(),

                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Edit')
                    ->slideOver(),

                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip('Delete'),
            ])
                
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
