<?php

namespace App\Filament\Resources\Closings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\ToggleComponent;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ClosingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Tutup Kas')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Tutup Kas')
                            ->required(),
                        TextInput::make('user_id')
                            ->label('Nama Petugas')
                            ->default(fn() => Auth()->user()?->name)
                            ->readOnly()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Perhitungan Uang Fisik')
                    ->description('Silakan inputkan jumlah uang fisik yang terkumpul')
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_uang_koin')
                            ->live()
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2),
                        TextInput::make('total_uang_kertas')
                            ->live()
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2),
                        Repeater::make('jumlah_uang_koin')
                            ->label('')
                            ->hiddenLabel(true)
                            ->live()
                            ->table([
                                TableColumn::make('Nominal Pecahan'),
                                TableColumn::make('Jumlah'),
                                TableColumn::make('sub Total')
                            ])
                            ->default([
                                    ['koin' => 100, 'banyak_koin' => 0, 'sub_total_koin' => 0],
                                    ['koin' => 200, 'banyak_koin' => 0, 'sub_total_koin' => 0],
                                    ['koin' => 500, 'banyak_koin' => 0, 'sub_total_koin' => 0],
                                    ['koin' => 1000, 'banyak_koin' => 0, 'sub_total_koin' => 0],
                                ])
                            ->schema([
                                TextInput::make('koin')
                                    ->dehydrated()
                                    ->prefixIcon(Heroicon::CircleStack)
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                    ->live(),
                                TextInput::make('banyak_koin')
                                    ->dehydrated()
                                    ->default(0)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state){
                                        $pecahan=$get('koin');
                                        if($state >= 0){
                                            $subtotal = $pecahan * $state;
                                            $set('sub_total_koin', $subtotal);
                                        }
                                    })
                                    ->live(),
                                TextInput::make('sub_total_koin')
                                    ->live()
                                    ->prefix('IDR')
                                    ->extraInputAttributes(['style' => 'font-weight: bold;'])
                                    ->readOnly()
                                    ->dehydrated()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('jumlah_uang_koin') ?? [];
                                $totalKoin = array_reduce($items, function ($carry, $item) {
                                    return $carry + (float) ($item['sub_total_koin'] ?? 0);
                                }, 0);

                                $set('total_uang_koin', $totalKoin);
                            })
                            ->deletable(false)
                            ->reorderable(false)
                            ->addable(false)
                            ->compact()
                            ->columns(1),

                        Repeater::make('jumlah_uang_kertas')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nominal Pecahan'),
                                TableColumn::make('Jumlah'),
                                TableColumn::make('sub Total'),
                            ])
                            ->default([
                                    ['kertas' => 1000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 2000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 5000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 10000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 20000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 50000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                    ['kertas' => 100000, 'banyak_kertas' => 0, 'sub_total_kertas' => 0],
                                ])
                            ->schema([
                                TextInput::make('kertas')
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                    ->prefixIcon(Heroicon::Banknotes)
                                    ->dehydrated()
                                    ->live(),
                                TextInput::make('banyak_kertas')
                                    ->dehydrated()
                                    ->default(0 )
                                    ->afterStateUpdated(function (Get $get, Set $set, $state){
                                        $pecahan=$get('kertas');
                                        if($state >= 0){
                                            $subtotal = $pecahan * $state;
                                            $set('sub_total_kertas', $subtotal);
                                        }
                                    })
                                    ->live(),
                                TextInput::make('sub_total_kertas')
                                    ->live()
                                    ->prefix('IDR')
                                    ->extraInputAttributes(['style' => 'font-weight: bold;'])
                                    ->readOnly()
                                    ->dehydrated()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('jumlah_uang_kertas') ?? [];
                                $totalKoin = array_reduce($items, function ($carry, $item) {
                                    return $carry + (float) ($item['sub_total_kertas'] ?? 0);
                                }, 0);

                                $set('total_uang_kertas', $totalKoin);
                            })
                            ->deletable(false)
                            ->reorderable(false)
                            ->addable(false)
                            ->compact()
                            ->columns(1),
                    ])
                    ->columnSpanFull(),

                    Section::make('Informasi Saldo Kas')
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('total_sistem')
                                ->required()
                                ->numeric(),
                            TextInput::make('total_fisik')
                                ->required()
                                ->numeric(),
                            TextInput::make('selisih')
                                ->required()
                                ->numeric(),
                            ToggleButtons::make('status')
                                ->options([
                                    'Buka' => 'Buka',
                                    'Tutup' => 'Tutup',
                                    'Selisih' => 'Selisih',
                                ])
                                ->colors([
                                    'Buka' => Color::Emerald,
                                    'Tutup' => Color::Rose,
                                    'Selisih' => Color::Amber,
                                ])
                                ->icons([
                                    'Buka' => Heroicon::LockOpen,
                                    'Tutup' => Heroicon::LockClosed,
                                    'Selisih' => Heroicon::ExclamationTriangle,
                                ])
                                ->inline()
                                ->default('Buka'),
                            Textarea::make('catatan')
                                ->required()
                                ->columnSpan(2),
                        ])
            ]);
    }


}
