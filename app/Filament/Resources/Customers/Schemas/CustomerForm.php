<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;

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
                ->label('NISN')
                ->prefixIcon('heroicon-m-identification')
                ->required() 
                ->maxLength(10)
                ->numeric()
                ->live(debounce: 500)
                ->rules([
                    fn () => function (string $attribute, $value, \Closure $fail) {
                        if (strlen((string) $value) !==10) {
                            $fail('NISN tidak boleh kurang dan lebih dari 10 digit');
                        }
                    },
                ])
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'min_digits' => 'NISN harus 10 digit.',
                    'max_digits' => 'NISN maksimal 10 digit.',
                    'unique' => 'NISN sudah terdaftar.',
                ])
                ->afterStateUpdated(function($livewire, TextInput $component){
                        $livewire->validateOnly($component->getStatePath());
                }),

                Radio::make('jenis_kelamin')
                ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                ->inline()
                ->required(),
               
                ToggleButtons::make('asal_sekolah')
                ->options([
                        'SMKS 1 PARAHYANGAN' => 'SMKS 1 Parahyangan',
                        'SMP PARAHYANGAN' => 'SMP Parahyangan',
                    ])
                ->inline()
                ->colors([
                        'SMK 1 PARAHYANGAN' => 'warning',
                        'SMP PARAHYANGAN' => 'info'
                    ])
                ->required(),

                TextInput::make('alamat_rumah')
                ->prefixIcon('heroicon-m-home')
                ->required(),

                TextInput::make('no_telepon')
                ->prefixIcon('heroicon-m-phone')
                ->tel()
                ->required()
                ->maxLength(13)
                ->live(debounce: 500)
                ->rules([
                    fn () => function (string $attribute, $value, \Closure $fail) {
                        $length = strlen((string) $value);
                        if ($length < 10 || $length > 13) {
                            $fail('Nomor telepon tidak boleh kurang dan lebih dari 10 sampai 13 digit.');
                        }
                    },
                ])
                ->afterStateUpdated(function ($livewire, TextInput $component) {
                    $livewire->validateOnly($component->getStatePath());
                }),
            ])
                ->columns(1);
            
    }
}
