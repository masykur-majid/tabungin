<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    Step::make('Data Nasabah')
                        ->icon('heroicon-m-user')
                        ->schema([
                            TextInput::make('nama')
                                ->label('Nama lengkap')
                                ->prefixIcon('heroicon-m-user')
                                ->required()
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^a-zA-Z\\s]/g, '')",
                                ]),

                            TextInput::make('nisn')
                                ->label('NISN')
                                ->prefixIcon('heroicon-m-identification')
                                ->required()
                                ->live(debounce: 500)
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)",
                                ])
                                ->rules([
                                    fn () => function (string $attribute, $value, \Closure $fail) {
                                        if (strlen((string) $value) !== 10) {
                                            $fail('NISN na kudu aya 10 digit.');
                                        }
                                    },
                                ])
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'unique' => 'NISN atos kadaftar.',
                                ])
                                ->afterStateUpdated(function ($livewire, TextInput $component) {
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
                                    'SMKS 1 PARAHYANGAN' => 'warning',
                                    'SMP PARAHYANGAN' => 'info',
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
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)",
                                ])
                                ->rules([
                                    fn () => function (string $attribute, $value, \Closure $fail) {
                                        if (strlen((string) $value) < 10 || strlen((string) $value) > 13) {
                                            $fail('Nomor telepon harus berisi antara 10 hingga 13 digit.');
                                        }
                                    },
                                ])
                                ->afterStateUpdated(function ($livewire, TextInput $component) {
                                    $livewire->validateOnly($component->getStatePath());
                                }),
                        ]),

                    Step::make('Rekening')
                        ->icon('heroicon-m-credit-card')
                        ->visibleOn('create')
                        ->schema([
                            Group::make()
                                ->relationship('account')
                                ->schema([
                                    TextInput::make('nomor_rekening')
                                ->label('Nomor rekening')
                                ->prefixIcon('heroicon-m-credit-card')
                                ->required()
                                ->maxLength(15)
                                ->live(debounce: 500)
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)",
                                ])
                                ->rules([
                                    fn () => function (string $attribute, $value, \Closure $fail) {
                                        if (strlen((string) $value) < 10 || strlen((string) $value) > 15) {
                                            $fail('Nomor rekening maksimal 15 digit.');
                                        }
                                    },
                                ])
                                ->unique(table: 'accounts', column: 'nomor_rekening')
                                ->validationMessages([
                                    'unique' => 'Nomor rekening sudah terdaftar.',
                                ])
                                ->afterStateUpdated(function ($livewire, TextInput $component) {
                                    $livewire->validateOnly($component->getStatePath());
                                }),

                                    TextInput::make('saldo')
                                        ->label('Saldo')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->minValue(1000)
                                        ->required(),

                                    Toggle::make('is_active')
                                        ->label('Is active')
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->default(true),
                                ]),
                        ]),

                ])->columnSpanFull(),
            ])
            ->columns(1);
    }
}