<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->required()
                    ->relationship('account', 'nomor_rekening'),
                Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name'),
                TextInput::make('no_slip')
                    ->default(function () {
        $latest = \App\Models\Transaction::latest('id')->first();
        $nextNumber = $latest ? $latest->id + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return "{$formattedNumber}/2026";
    })
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                DatePicker::make('tanggal')
                    ->required()
                    ->MaxDate(now()),
                TextInput::make('jenis_transaksi')
                    ->required()
                    ->default('setoran'),
                TextInput::make('jumlah_transaksi')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'), // Ini bikin tulisan Rp di sebelah kiri input
            ]);
    }
}
