<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum Ada Nasabah')
            ->emptyStateDescription('Catat informasi lengkap tentang nasabah')
            ->emptyStateIcon('fas-money-bill-transfer')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Input Nasabah Baru')
                    ->icon('fas-user-plus')
                    ->url(fn() => AccountResource::getUrl('create'))
                    ->button()
            ])
            ->columns([
               TextColumn::make('customer.nama')
                    ->sortable(),

                TextColumn::make('nomor_rekening')
                    ->searchable(),

                TextColumn::make('saldo')
                    ->numeric()
                    ->sortable(),
                    
                ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->onIcon(Heroicon::Check)
                    ->offIcon(Heroicon::XMark)
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
          ->recordActions([
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
            ])
            ->searchPlaceholder('Cari nomor rekening nasabah...');
    }
}
