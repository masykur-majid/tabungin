<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('NISN')
                    ->required(),
                Select::make('jenis_kelamin')
                    ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                    ->required(),
                Select::make('asal_sekolah')
                    ->options([
            'SMKS 1 PARAHYANGAN' => 'S m k s1 p a r a h y a n g a n',
            'SMP PARAHYANGAN' => 'S m p p a r a h y a n g a n',
        ])
                    ->required(),
                TextInput::make('alamat_rumah')
                    ->required(),
                TextInput::make('no_telepon')
                    ->tel()
                    ->required(),
            ]);
    }
}
