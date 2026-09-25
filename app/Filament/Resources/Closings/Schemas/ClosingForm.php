<?php

namespace App\Filament\Resources\Closings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class ClosingForm
{
    public static function configure(Schema $schema): Schema
    {
        $moneyMask = RawJs::make('$money($input, ",", ".", 0)');

        return $schema
            ->components([

                // INFORMASI TUTUP KAS
                Section::make('Informasi Tutup Kas')
                    ->schema([

                        DatePicker::make('tanggal')
                            ->label('Tanggal Tutup Kas')
                            ->default(now())
                            ->required(),

                        TextInput::make('user_id')
                            ->label('Nama Petugas')
                            ->formatStateUsing(fn () => Auth::user()?->name)
                            ->dehydrated(false)
                            ->readOnly()
                            ->required(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // PERHITUNGAN UANG FISIK
                Section::make('Perhitungan Uang Fisik')
                    ->description('Silakan inputkan jumlah uang fisik yang terkumpul')
                    ->columns(2)
                    ->schema([

                        // TOTAL UANG KOIN
                        TextInput::make('total_uang_koin')
                            ->label('Total Uang Koin')
                            ->prefix('Rp')
                            ->default(0)
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->live(),

                        // TOTAL UANG KERTAS
                        TextInput::make('total_uang_kertas')
                            ->label('Total Uang Kertas')
                            ->prefix('Rp')
                            ->default(0)
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->live(),

                        // RINCIAN UANG KOIN
                        Repeater::make('jumlah_uang_koin')
                            ->label('Rincian Uang Koin')
                            ->live()
                            ->table([
                                TableColumn::make('Nominal Pecahan'),
                                TableColumn::make('Jumlah'),
                                TableColumn::make('Subtotal'),
                            ])
                            ->default([
                                [
                                    'koin' => 100,
                                    'banyak_koin' => 0,
                                    'sub_total_koin' => 0,
                                ],
                                [
                                    'koin' => 200,
                                    'banyak_koin' => 0,
                                    'sub_total_koin' => 0,
                                ],
                                [
                                    'koin' => 500,
                                    'banyak_koin' => 0,
                                    'sub_total_koin' => 0,
                                ],
                                [
                                    'koin' => 1000,
                                    'banyak_koin' => 0,
                                    'sub_total_koin' => 0,
                                ],
                            ])
                            ->schema([

                                TextInput::make('koin')
                                    ->label('Pecahan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated(),

                                TextInput::make('banyak_koin')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        function (Get $get, Set $set, $state) {

                                            $pecahan = (int) $get('koin');
                                            $jumlah = max(0, (int) $state);

                                            $subtotal = $pecahan * $jumlah;

                                            $set('sub_total_koin', $subtotal);
                                        }
                                    ),

                                TextInput::make('sub_total_koin')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->default(0),

                            ])
                            ->afterStateUpdated(
                                function (Get $get, Set $set) {

                                    $items = $get('jumlah_uang_koin') ?? [];

                                    $total = array_reduce(
                                        $items,
                                        function ($carry, $item) {
                                            return $carry
                                                + (int) ($item['sub_total_koin'] ?? 0);
                                        },
                                        0
                                    );

                                    $set('total_uang_koin', $total);

                                    $totalKertas = (int) ($get('total_uang_kertas') ?? 0);

                                    $set('total_fisik', $total + $totalKertas);

                                    $totalSistem = (int) ($get('total_sistem') ?? 0);

                                    $set('selisih', ($total + $totalKertas) - $totalSistem);
                                }
                            )
                            ->deletable(false)
                            ->reorderable(false)
                            ->addable(false)
                            ->compact()
                            ->columns(1),

                        // RINCIAN UANG KERTAS
                        Repeater::make('jumlah_uang_kertas')
                            ->label('Rincian Uang Kertas')
                            ->live()
                            ->table([
                                TableColumn::make('Nominal Pecahan'),
                                TableColumn::make('Jumlah'),
                                TableColumn::make('Subtotal'),
                            ])
                            ->default([
                                [
                                    'kertas' => 1000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 2000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 5000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 10000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 20000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 50000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                                [
                                    'kertas' => 100000,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ],
                            ])
                            ->schema([

                                TextInput::make('kertas')
                                    ->label('Pecahan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated(),

                                TextInput::make('banyak_kertas')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        function (Get $get, Set $set, $state) {

                                            $pecahan = (int) $get('kertas');
                                            $jumlah = max(0, (int) $state);

                                            $subtotal = $pecahan * $jumlah;

                                            $set('sub_total_kertas', $subtotal);
                                        }
                                    ),

                                TextInput::make('sub_total_kertas')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->default(0),

                            ])
                            ->afterStateUpdated(
                                function (Get $get, Set $set) {

                                    $items = $get('jumlah_uang_kertas') ?? [];

                                    $total = array_reduce(
                                        $items,
                                        function ($carry, $item) {
                                            return $carry
                                                + (int) ($item['sub_total_kertas'] ?? 0);
                                        },
                                        0
                                    );

                                    $set('total_uang_kertas', $total);

                                    $totalKoin = (int) ($get('total_uang_koin') ?? 0);

                                    $totalFisik = $total + $totalKoin;

                                    $set('total_fisik', $totalFisik);

                                    $totalSistem = (int) ($get('total_sistem') ?? 0);

                                    $set('selisih', $totalFisik - $totalSistem);
                                }
                            )
                            ->deletable(false)
                            ->reorderable(false)
                            ->addable(false)
                            ->compact()
                            ->columns(1),

                    ])
                    ->columnSpanFull(),

                // INFORMASI SALDO KAS
                Section::make('Informasi Saldo Kas')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([

                        TextInput::make('total_sistem')
                            ->label('Total Uang Sistem')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                function (Get $get, Set $set, $state) {

                                    $totalFisik = (int) ($get('total_fisik') ?? 0);

                                    $totalSistem = (int) ($state ?? 0);

                                    $set('selisih', $totalFisik - $totalSistem);
                                }
                            ),

                        TextInput::make('total_fisik')
                            ->label('Total Uang Fisik')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated()
                            ->required(),

                        TextInput::make('selisih')
                            ->label('Selisih Kas')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated()
                            ->required(),

                        ToggleButtons::make('status')
                            ->label('Status')
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
                            ->default('Buka')
                            ->required(),
                 
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan tutup kas')
                            ->default('-')
                            ->required()
                            ->columnSpan(2),

                    ]),

            ]);
    }
}