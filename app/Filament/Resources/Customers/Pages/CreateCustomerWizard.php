<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerWizard extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = CustomerResource::class;

    protected function getSteps(): array
    {
        return [
            Step::make('Data Nasabah')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama')
                        ->required()
                        ->validationMessages([
                        ])
                        ->live(onBlur: true),

                    TextInput::make('nisn')
                        ->label('NISN')
                        ->required()
                        ->numeric()
                        ->length(10)
                        ->validationMessages([
                            'numeric' => 'NISN hanya boleh berupa angka.',
                            'length' => 'NISN harus persis 10 digit.',
                        ])
                        ->live(onBlur: true),

                    TextInput::make('no-telepon')
                        ->statePath('no-telepon')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->required(),

                    ToggleButtons::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options([
                            'Laki-laki' => 'Laki-laki',
                            'Perempuan' => 'Perempuan',
                        ])
                        ->colors([
                            'Laki-laki' => 'info',
                            'Perempuan' => 'danger',
                        ])
                        ->inline()
                        ->required(),

                    TextInput::make('alamat')
                        ->label('Alamat Rumah')
                        ->required(),
                ]),

            Step::make('Data Rekening')
                ->schema([
                    Group::make()
                        ->relationship('account')
                        ->schema([
                            TextInput::make('nomor_rekening')
                                ->label('Nomor Rekening')
                                ->required(),

                            TextInput::make('saldo')
                                ->label('Saldo Awal')
                                ->numeric()
                                ->default(0),
                        ]),
                ]),
        ];
    }
}