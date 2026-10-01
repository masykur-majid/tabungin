<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ToggleButtons;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'nama')
                    ->prefixIcon('heroicon-m-user')
                    ->required(),

               TextInput::make('nomor_rekening')
                ->label('Nomor rekening')
                ->required()
                ->numeric()
                ->rules(['min_digits:15', 'max_digits:15'])
                ->validationMessages([
                    'required' => 'Nomor rekening wajib diisi.',
                    'min_digits' => 'Nomor rekening harus 15 digit.',
                    'max_digits' => 'Nomor rekening tidak boleh lebih dari 15 digit.',
                ])
                ->unique(ignoreRecord: true),

                TextInput::make('saldo')
                    ->required()
                    ->prefix('Rp')
                    ->rules(['min:0'])
                    ->numeric()
                    ->validationMessages([
                    'min' => 'Saldo tidak boleh kurang dari 0.',
                    
                    ]),
               ToggleButtons::make('is_active')
                ->label('Is active')
                ->boolean()
                ->inline()
                ->icons([
                    true => 'heroicon-m-check-circle',
                    false => 'heroicon-m-x-circle',
                ])
                ->colors([
                    true => 'success',
                    false => 'danger',
                ])
                ->required(),
            ]);
    }
}
