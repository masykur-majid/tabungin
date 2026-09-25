<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Doctrine\Inflector\Rules\English\Rules;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Laravel\Mcp\Support\ValidationMessages;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->required(),
                TextInput::make('nomor_rekening')
                    ->label('Nomor Rekening')
                    ->required()
                    ->Rules(['max_digits: 15 | min_digits: 15'])
                    ->ValidationMessages([
                        'max_digits' => 'Nomor rekening tidak boleh lebih dari 15 digit',
                        'min_digits' => 'Nomor rekening tidak boleh kurang dari 15 digit',
                        'required'   => 'Nomor rekening harus diisi',
                    ])
                    ->unique(ignoreRecord: true),
                TextInput::make('saldo')
                    ->required()
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
