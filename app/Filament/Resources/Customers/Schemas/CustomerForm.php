<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Contracts\HasForms;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                ->prefixIcon('heroicon-m-user')
                ->required(),
              TextInput::make('NISN')
                ->prefixIcon('heroicon-m-identification')
                ->required()
                ->numeric()
                ->live(onBlur: True)
                ->rules(['min_digits:10', 'max_digits:10'])
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'min_digits' => 'NISN harus 10 digit.',
                    'max_digits' => 'NISN maksimal 10 digit.',
                    'unique' => 'NISN sudah terdaftar.',
                ])
                ->afterStateUpdated(function ($livewire, TextInput $component) {
                    $livewire->validateOnly($component->getStatePath());
                }),
                Radio::make('jenis_kelamin')
                ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                ->inline(false)
                ->required(),
                Radio::make('asal_sekolah')
                ->options([
                    'SMKS 1 PARAHYANGAN' => 'SMKS 1 Parahyangan',
                    'SMP PARAHYANGAN' => 'SMP Parahyangan',
                    ])
                    ->required(),
                TextInput::make('alamat_rumah')
                ->prefixIcon('heroicon-m-home')
                ->required(),
                TextInput::make('no_telepon')
                ->prefixIcon('heroicon-m-phone')
                ->tel()
                ->required()
                ->rules(['digits_between:10,13'])
                ->validationMessages([
                        'digits_between' => 'Nomor telepon harus 10-13 digit.',
                        'required' => 'Nomor telepon wajib diisi.',
                    ]),
                    
            ])
            ->columns(1);
            
    }
}
