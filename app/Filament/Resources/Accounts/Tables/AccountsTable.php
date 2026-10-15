<?php

namespace App\Filament\Resources\Accounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Customers\CustomerResource;


class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('customer.nama')
                    ->label('Nasabah')
                    ->sortable(),

                TextColumn::make('nomor_rekening')
                    ->copyable()
                    ->copyMessage('Nomor rekening disalin')
                    ->icon('heroicon-o-document-duplicate')
                    ->iconColor('blue')
                    ->iconPosition('after'),
                TextColumn::make('saldo')
                    ->numeric()
                    ->sortable()
                    ->numeric(decimalPlaces: 0, decimalSeparator: ',', thousandsSeparator: '.'),
                ToggleColumn::make('is_active')
                ->onIcon('heroicon-m-check')
                ->offIcon('heroicon-m-x-mark')
                ->onColor('success')
                ->offColor('danger'),
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
                    ->tooltip('View'),

                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Edit'),

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
