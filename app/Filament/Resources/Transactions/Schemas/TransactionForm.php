<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->label('No Slip')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(false),

                DatePicker::make('tanggal')
                    ->required(),

                TextInput::make('jenis_transaksi')
                    ->required()
                    ->default('setoran'),

                TextInput::make('jumlah_transaksi')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'), // ini bikin tulisan Rp di sebelah kiri input
            ]);
    }
}