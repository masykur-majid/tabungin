<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;

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
                ->length(15)
                ->validationMessages([
                    'required' => 'Nomor rekening wajib diisi.',
                    'length' => 'Nomor rekening harus 15 digit.',
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
               Toggle::make('is_active')
                ->onIcon(Heroicon::Check)
                ->offIcon(Heroicon::XMark)
                ->onColor('success')
                ->offColor('danger')
                
                ->required(),
            ])
            ->columns(1);
    }
}
