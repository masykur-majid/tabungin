<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('account.id')
                    ->label('Account'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('no_slip'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('jenis_transaksi'),
                TextEntry::make('jumlah_transaksi')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
