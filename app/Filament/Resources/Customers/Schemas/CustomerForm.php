<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Radio;
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
                    ->required()
                    ->numeric()
                    ->length(10)
                    ->unique(ignoreRecord: true)
                    ->ValidationMessages([
                    'digits'=> 'NISN harus 10 digit.',
                    'unique'=>'NISN sudah terdaftar'
                    ]),
                Radio::make('jenis_kelamin')
                    ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                    ->inline()
                    ->required(),
                Radio::make('asal_sekolah')
                    ->options([
                    'SMKS 1 PARAHYANGAN' => 'S M K S 1 P A R A H Y A N G A N',
                    'SMP PARAHYANGAN' => 'S M P P A R A H Y A N G A N',
        ])
                    ->required(),
                TextInput::make('alamat_rumah')
                    ->required(),
                TextInput::make('no_telepon')
                    ->tel()
                    ->required()
                    ->rules(['digits_between:10,13'])
                    ->validationMessages([
                        'digits_between' => 'Nomor telepon harus 10-13 digit angka',
                        'required' => 'Nomor telepon wajib diisi.',
                    ]),
            ])
            ->columns(1);
    }
}
