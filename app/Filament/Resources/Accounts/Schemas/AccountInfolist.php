<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.nama')
                    ->label('Customer'),
                TextEntry::make('nomor_rekening'),
                TextEntry::make('saldo')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
            ]);
    }
}
