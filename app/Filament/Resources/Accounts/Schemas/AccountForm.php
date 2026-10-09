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
                    ->numeric()
                    ->maxLength(15)
                    ->live(debounce: 500)
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {
                            if (strlen((string) $value) !== 15) {
                                $fail('Nomor rekening harus 15 digit.');
                            }
                        },
                    ])
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'Nomor rekening wajib diisi.',
                        'unique' => 'Nomor rekening sudah terdaftar.',
                    ])
                    ->afterStateUpdated(function ($livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                    }),

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
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),
                ])
                    ->columns(1);
    }
}
