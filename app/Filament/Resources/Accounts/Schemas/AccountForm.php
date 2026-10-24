<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Nasabah')
                    ->relationship('customer', 'nama')
                    ->placeholder('Pilih nasabah')
                    ->prefixIcon('heroicon-m-user')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('nomor_rekening')
                    ->label('Nomor Rekening')
                    ->required()
                    ->inputmode('numeric')
                    ->mask('999999999999999')
                    ->inputMode('numeric')
                    ->rules(['digits:15'])
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Nomor rekening sudah terdaftar.',
                        'digits' => 'Nomor rekening harus berjumlah 15 digit.',
                    ])
                    ->extraInputAttributes([
                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)",
                    ]),

                TextInput::make('saldo')
                    ->label('Saldo')
                    ->required()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(1000)
                    ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : null)
                    ->validationMessages([
                        'min' => 'Saldo tidak boleh kurang dari Rp 1.000.',
                    ]),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->onIcon(Heroicon::Check)
                    ->offIcon(Heroicon::XMark)
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->required(),
            ])
            ->columns(1);
    }
}