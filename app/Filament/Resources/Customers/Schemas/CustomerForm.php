<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('NISN')
                    ->label('NISN')
                    ->required(),
                Radio::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-Laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->inline()
                    ->required(),
                ToggleButtons::make('asal_sekolah')
                    ->options([
                        'SMP Parahyangan' => 'SMP Parahyangan',
                        'SMKS 1 Parahyangan' => 'SMKS 1 Parahyangan',
                    ])
                    ->colors([
                        'SMP Parahyangan' => Color::Blue,
                        'SMKS 1 Parahyangan' => Color::Amber,
                    ])
                    ->required()
                    ->inline(),
                TextInput::make('alamat_rumah')
                    ->required(),
                TextInput::make('no_telepon')
                    ->tel()
                    ->required(),
            ])
            ->columns(1);
    }
}
