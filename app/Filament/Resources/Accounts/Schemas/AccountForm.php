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
                    ->rules(['min_digits','max_digits:15'])
                    ->validationMessages([
                        'max_digits' => 'Nomor rekening maksimal 15 digit.',
                        'required' => 'Nomor rekening wajid diisi',
                        'min_digits' => 'No rekening tidak boleh minus atau kurang dari 0.',
                    ]),
                TextInput::make('saldo')
                    ->required()
                    ->numeric()
                    ->rules(['min:0']) // Mencegah nilai minus/negatif
                    ->validationMessages([
                    'min' => 'Saldo tidak boleh minus atau kurang dari 0.',
                ]),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),
            ])
            ->columns(1);
    }
}
