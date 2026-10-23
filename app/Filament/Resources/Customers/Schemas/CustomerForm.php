<?php

namespace App\Filament\Resources\Customers\Schemas;

use Closure;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Support\RawJs;

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

                            TextInput::make('nik')
                                ->label('NIK')
                                ->required()
                                ->live(debounce: 500)
                                ->maxLength(16)
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)",
                                ])
                                ->rules([
                                    fn () => function (string $attribute, $value, Closure $fail): void {
                                        if (strlen((string) $value) !== 16) {
                                            $fail('NIK harus 16 digit.');
                                        }
                                    },
                                ])
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'unique' => 'NIK sudah terdaftar.',
                                ])
                                ->afterStateUpdated(fn ($livewire, TextInput $component) => $livewire->validateOnly($component->getStatePath())),

                            TextInput::make('nama')
                                ->label('Nama Lengkap')
                                ->prefixIcon('heroicon-m-user')
                                 ->dehydrateStateUsing(fn ($state) => Str::title($state))
                                ->required()
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^a-zA-Z\\s]/g, '')",
                                ]),

                            ToggleButtons::make('asal_sekolah')
                                ->label('Asal Sekolah')
                                ->options([
                                    'SMKS 1 PARAHYANGAN' => 'SMKS 1 PARAHYANGAN',
                                    'SMP PARAHYANGAN' => 'SMP PARAHYANGAN',
                                    'GURU PARAHYANGAN' => 'GURU PARAHYANGAN',
                                ])
                                ->colors([
                                    'SMKS 1 PARAHYANGAN' => 'warning',
                                    'SMP PARAHYANGAN' => 'info',
                                    'GURU PARAHYANGAN' => 'success',
                                ])
                                ->inline()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('nik', null))
                                ->required(),

                            Radio::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->inline()
                                ->required()
                                ->options([
                                    'Laki-laki' => 'Laki-laki',
                                    'Perempuan' => 'Perempuan',
                                ]),

                            TextInput::make('alamat_rumah')
                                ->label('Alamat Rumah')
                                ->prefixIcon('heroicon-m-home')
                                ->required(),

                            TextInput::make('no_telepon')
                                ->label('Nomor Telepon')
                                ->prefixIcon('heroicon-m-phone')
                                ->tel()
                                ->required()
                                ->maxLength(13)
                                ->live(debounce: 500)
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)",
                                ])
                                ->rules([
                                    fn () => function (string $attribute, $value, Closure $fail): void {
                                        $length = strlen((string) $value);
                                        if ($length < 10 || $length > 13) {
                                            $fail('Nomor telepon harus berisi antara 10 hingga 13 digit.');
                                        }
                                    },
                                ])
                                ->afterStateUpdated(fn ($livewire, TextInput $component) => $livewire->validateOnly($component->getStatePath())),
                        ]),

                    Step::make('Rekening')
                        ->icon('heroicon-m-credit-card')
                        ->visibleOn('create')
                        ->schema([
                            Group::make()
                                ->relationship('account')
                                ->schema([
                                    TextInput::make('nomor_rekening')
                                        ->label('Nomor Rekening')
                                        ->prefixIcon('heroicon-m-credit-card')
                                        ->required()
                                        ->maxLength(15)
                                        ->live(debounce: 500)
                                        ->extraInputAttributes([
                                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)",
                                        ])
                                        ->rules([
                                            fn () => function (string $attribute, $value, Closure $fail): void {
                                                $length = strlen((string) $value);
                                                if ($length < 10 || $length > 15) {
                                                    $fail('Nomor rekening harus berisi antara 10 hingga 15 digit.');
                                                }
                                            },
                                        ])
                                        ->unique(table: 'accounts', column: 'nomor_rekening')
                                        ->validationMessages([
                                            'unique' => 'Nomor rekening sudah terdaftar.',
                                        ])
                                        ->afterStateUpdated(fn ($livewire, TextInput $component) => $livewire->validateOnly($component->getStatePath())),

                                    TextInput::make('saldo')
                                        ->label('Saldo')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                        ->minValue(1000)
                                        ->stripCharacters('.')
                                        ->required(),

                                    Toggle::make('is_active')
                                        ->label('Status Aktif')
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