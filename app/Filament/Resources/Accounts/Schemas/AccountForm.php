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
                    ->prefix('Rp')
                    ->required()
                    ->rules(['max_digits:15'])
                    ->validationMessages([
                    'max_digits' => 'Nomor rekening maksimal 15 digit.',
                    'required' => 'Nomor rekening wajib diisi.',
                ])     
                    ->unique(ignoreRecord: true),
                TextInput::make('saldo')
                    ->required()
                    ->numeric(),
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
