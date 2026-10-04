<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Hidden;
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
                Hidden::make('customer_id')
                    ->label('Customer')
                    ->default(function($livewire, $record){
                        if(method_exists($livewire, 'getOwnerRecord')){
                            return $livewire->getOwnerRecord()?->id ?? '-';
                        }
                        return $record?->customer?->id ?? '-';
                    })
                    ->required(),

                TextInput::make('nisn_nasabah')
                    ->label('NISN')
                    ->default(function($livewire, $record){
                        if(method_exists($livewire, 'getOwnerRecord')){
                            return $livewire->getOwnerRecord()?->NISN ?? '-';
                        }
                        return $record?->customer?->NISN ?? '-';
                    })
                    ->readOnly()
                    ->dehydrated(false),

                TextInput::make('nama_nasabah')
                    ->label('Nama Nasabah')
                    ->default(function($livewire, $record){
                        if(method_exists($livewire, 'getOwnerRecord')){
                            return $livewire->getOwnerRecord()?->nama ?? '-';
                        }
                        return $record?->customer?->nama ?? '-';
                    })
                    ->readOnly()
                    ->dehydrated(false),

                TextInput::make('nomor_rekening')
                    ->label('Nomor rekening')
                    ->required()
                    ->length(15)
                    ->validationMessages([
                        'required' => 'Nomor rekening wajib diisi.',
                        'length' => 'Nomor rekening harus 15 digit.',
                    ])
                    ->unique(ignoreRecord: true),
              

                TextInput::make('saldo')
                    ->required()
                    ->prefix('Rp')
                    ->rules(['min:0'])
                    ->numeric()
                    ->validationMessages([
                        'min' => 'Saldo tidak boleh kurang dari 0.',
                    ]),
               Toggle::make('is_active')
                    ->label('Aktifkan Rekening?')
                    ->onIcon(Heroicon::Check)
                    ->offIcon(Heroicon::XMark)
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->required(),
            ])
            ->columns(2);
    }
}
