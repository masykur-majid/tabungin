<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('NISN')
                ->required()
                ->numeric()
                ->length(10)
                ->unique(ignoreRecord: true)
                ->validationMessages([
                 'digits' => 'NISN maksimal 10 digit.',
                 'unique' => 'NISN sudah terdaftar.',
        ]),
                Select::make('jenis_kelamin')
                    ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                    ->required(),
                    Radio::make('asal_sekolah')
                    ->options([
                        'SMKS 1 PARAHYANGAN' => 'SMKS 1 Parahyangan',
                        'SMP PARAHYANGAN' => 'SMP Parahyangan',
                    ])
                    ->required(),
                TextInput::make('alamat_rumah')
                    ->required(),
             TextInput::make('no_telepon')
                    ->tel()
                    ->required()
                    ->rules(['digits_between:10,13'])
                    ->validationMessages([
                        'digits_between' => 'Nomor telepon harus 10-13 digit angka.',
                        'required' => 'Nomor telepon wajib diisi.',
                    ]),
            ]);
    }
}
