<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                ->label('Nomor rekening')
                ->required()
                ->rules(['max_digits:15']) // maksimal 15 digit angka
                ->validationMessages([
                    'max_digits' => 'Nomor rekening maksimal 15 digit.',
                    'required' => 'Nomor rekening wajib diisi.',
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
