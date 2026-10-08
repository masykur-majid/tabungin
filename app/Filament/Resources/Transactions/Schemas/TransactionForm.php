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
                    ->relationship(
                        name: 'account', 
                        titleAttribute: 'nomor_rekening', 
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)                     
                    )
                    ->searchable()
                    ->preload()
                    ->validationMessages([
                        'required' => 'Pilih Nomor Rekening',
                    ]),
                TextInput::make('jumlah_transaksi')
                    ->required()
                    ->prefix('Rp') 
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->minValue(1000)
                    ->validationMessages([
                        'min' => 'Setoran Minimal Rp 1.000',
                    ]),
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
                    ->default(today())
                    ->maxDate(today())
                    ->minDate(today())
                    ->readOnly(),

                TextInput::make('jenis_transaksi')
                    ->required()
                    ->default('setoran')
                    ->readOnly(),
                    
            ]);
    }
}