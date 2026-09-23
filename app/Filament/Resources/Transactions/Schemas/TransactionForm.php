<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        DatePicker::make('tanggal')
                            ->required(),
                        Hidden::make('user_id')
                            ->required()
                            ->default(fn() => Auth()->user()?->id)
                            ->disabled()
                            ->dehydrated(true),
                        TextInput::make('user_name')
                            ->label('Petugas')
                            ->default(fn() => Auth()->user()?->name)
                            ->readonly()
                            ->dehydrated(false),
                        TextInput::make('no_slip')
                            ->required(),
                    ]),
                Section::make('')
                    ->schema([
                        Select::make('account_id')
                            ->label('Nomor Rekening')
                            ->live()
                            ->searchable()
                            ->preload()
                            ->relationship('account', 'nomor_rekening')
                            ->afterStateUpdated(function ($state, $record, Set $set){
                                $selectedAccount = Account::with('customer')->find($state);
                                $set('account_name', $selectedAccount?->customer?->nama);
                            })
                            ->required()
                            ->extraInputAttributes(['style' => 'font-weight: bold; font-size: 2em;']),
                        TextInput::make('account_name')
                            ->label('Nama Pemilik Rekening')
                            ->dehydrated(false)
                            ->readOnly()
                            ->required(),
                    ]),
                Section::make('')
                    ->schema([
                        ToggleButtons::make('jenis_transaksi')
                            ->required()
                            ->options([
                                'Setoran' => 'Setoran',
                                'Penarikan' => 'Penarikan'
                            ])
                            ->colors([
                                'Setoran' => Color::Blue,
                                'Penarikan' => Color::Rose
                            ])
                            ->inline()
                            ->default('Setoran'),
                        TextInput::make('jumlah_transaksi')
                            ->required()
                            ->prefix('IDR')
                            ->numeric()
                            ->extraInputAttributes(['style' => 'font-weight: bold; font-size: 2em;'])
                            ->placeholder('–')
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2),
                    ])

            ])
            ->columns(2);
    }
}
