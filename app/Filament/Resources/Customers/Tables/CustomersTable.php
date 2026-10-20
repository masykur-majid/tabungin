<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use App\Models\Account;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
            ->label('Nama')
            ->searchable(),

       TextColumn::make('nik')
            ->label('NIK')
            ->placeholder('-')
            ->searchable(),

        TextColumn::make('asal_sekolah')
            ->label('Asal sekolah')
            ->badge()
            ->color(fn (?string $state): string => match (true) {
                    str_contains(strtolower((string) $state), 'smk') => 'warning',
                    str_contains(strtolower((string) $state), 'smp') => 'info',
                    str_contains(strtolower((string) $state), 'guru') => 'success', 
                    default => 'gray',
                })
            ->searchable(),

        TextColumn::make('jenis_kelamin')
            ->label('Jenis Kelamin')
            ->badge()
            ->color(fn (?string $state): string => match (true) {
                str_contains(strtolower((string) $state), 'perempuan') => 'danger',
                str_contains(strtolower((string) $state), 'laki')      => 'info', 
                default => 'gray',
            })
                ->toggleable()
            ->toggleable(isToggledHiddenByDefault: true),
                 

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
                    ->tooltip('Delete')
                    ->before(function (DeleteAction $action, $record) {
                        if (Account::where('customer_id', $record->id)->exists()) {
                            Notification::make()
                                ->title('Nasabah tidak bisa dihapus')
                                ->body('Hapus rekening nasabah ini terlebih dahulu.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }  
        }),
            ])
                
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
