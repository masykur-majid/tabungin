<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\RawJs;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('Nomor Rekening')
                    ->required()
                    ->relationship('account', 'nomor_rekening'),

                Select::make('user_id')
                    ->label('Petugas')
                    ->relationship('user', 'name')
                    ->prefixIcon('heroicon-o-user')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('no_slip')
                    ->label('No Slip')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(false),

                DatePicker::make('tanggal')
                    ->required()
                    ->maxDate(now()),

                TextInput::make('jenis_transaksi')
                    ->required()
                    ->default('setoran'),

                TextInput::make('jumlah_transaksi')
                    ->required()
                    ->prefix('Rp') 
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                    
            ]);
    }
}