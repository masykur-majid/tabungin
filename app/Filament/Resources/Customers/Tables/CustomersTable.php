<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions\Action;
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
            ->emptyStateHeading('')
            ->emptyStateDescription('Belum ada nasabah yang tercatat di sistem')
            ->emptyStateIcon('fas-id-card')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Input Nasabah Baru')
                    ->icon('fas-user-plus')
                    ->url(fn() => CustomerResource::getUrl('create'))
                    ->button()
            ])
            ->columns([
                
                TextColumn::make('NISN')
                    ->label('NISN')
                    ->searchable(),

                TextColumn::make('nama')
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
                        'SMKS 1 PARAHYANGAN' => 'warning', 
                        'SMP PARAHYANGAN'    => 'info', 
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
           
          ->recordActions([
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
