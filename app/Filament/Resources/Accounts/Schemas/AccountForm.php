<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->getOptionLabelFromRecordUsing(fn ($record)=> "$record->NISN - {$record->nama}")
                    ->preload()
                    ->searchable()
                    ->required(),
                TextInput::make('nomor_rekening')
                    ->required(),
                TextInput::make('saldo')
                    ->required()
                    ->prefix('Rp')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->numeric(),
                Toggle::make('is_active')
                    ->required()
                    ->onIcon(Heroicon::Check)
                    ->offIcon(Heroicon::XMark)
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->columns(1);
    }
}
