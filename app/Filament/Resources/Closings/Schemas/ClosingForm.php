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
use Illuminate\Support\Facades\Auth;

class ClosingForm
{
    /** Hitung ulang dari jumlah dan pecahan, bukan dari subtotal yang mungkin belum terbarui. */
    private static function hitungKas(Get $get, Set $set): void
    {
        $totalKoin = 0;
        foreach (($get('jumlah_uang_koin') ?? []) as $item) {
            $totalKoin += (int) ($item['koin'] ?? 0) * max(0, (int) ($item['banyak_koin'] ?? 0));
        }

        $totalKertas = 0;
        foreach (($get('jumlah_uang_kertas') ?? []) as $item) {
            $totalKertas += (int) ($item['kertas'] ?? 0) * max(0, (int) ($item['banyak_kertas'] ?? 0));
        }

        $totalFisik = $totalKoin + $totalKertas;
        $set('total_uang_koin', $totalKoin);
        $set('total_uang_kertas', $totalKertas);
        $set('total_fisik', $totalFisik);
        $set('selisih', $totalFisik - (int) ($get('total_sistem') ?? 0));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Tutup Kas')
                ->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Tutup Kas')
                        ->default(fn () => today()->toDateString())
                        ->minDate(fn () => today()->toDateString())
                        ->maxDate(fn () => today()->toDateString())
                        ->native(false)
                        ->unique(table: 'closings', column: 'tanggal', ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'Tanggal ini sudah digunakan untuk tutup kas!',
                        ])
                        ->required(),

                    TextInput::make('user_id')
                        ->label('Nama Petugas')
                        ->formatStateUsing(fn () => Auth::user()?->name)
                        ->dehydrated(false)
                        ->readOnly(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Perhitungan Uang Fisik')
                ->description('Masukkan jumlah lembar atau keping pada setiap pecahan. Subtotal dan total dihitung otomatis.')
                ->schema([
                    Section::make('Uang Koin')
                        ->description('Isi jumlah keping uang koin yang diterima.')
                        ->schema([
                            Repeater::make('jumlah_uang_koin')
                                ->label('Rincian Uang Koin')
                                ->table([
                                    TableColumn::make('Pecahan'),
                                    TableColumn::make('Jumlah Keping'),
                                    TableColumn::make('Subtotal'),
                                ])
                                ->default(collect([100, 200, 500, 1000])->map(fn ($nominal) => [
                                    'koin' => $nominal,
                                    'banyak_koin' => 0,
                                    'sub_total_koin' => 0,
                                ])->all())
                                ->schema([
                                    TextInput::make('koin')
                                        ->label('Pecahan')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated(),

                                    TextInput::make('banyak_koin')
                                        ->label('Jumlah Keping')
                                        ->integer()
                                        ->minValue(0)
                                        ->default(0)
                                        ->live(debounce: 400)
                                        ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                            $set('sub_total_koin', (int) ($get('koin') ?? 0) * max(0, (int) $state));
                                        }),

                                    TextInput::make('sub_total_koin')
                                        ->label('Subtotal')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->default(0)
                                        ->readOnly()
                                        ->dehydrated(),
                                ])
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::hitungKas($get, $set))
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->compact(),

                            TextInput::make('total_uang_koin')
                                ->label('TOTAL UANG KOIN')
                                ->prefix('Rp')
                                ->numeric()
                                ->default(0)
                                ->readOnly()
                                ->dehydrated(),
                        ])
                        ->columnSpanFull(),

                    Section::make('Uang Kertas')
                        ->description('Isi jumlah lembar uang kertas yang diterima.')
                        ->schema([
                            Repeater::make('jumlah_uang_kertas')
                                ->label('Rincian Uang Kertas')
                                ->table([
                                    TableColumn::make('Pecahan'),
                                    TableColumn::make('Jumlah Lembar'),
                                    TableColumn::make('Subtotal'),
                                ])
                                ->default(collect([1000, 2000, 5000, 10000, 20000, 50000, 100000])->map(fn ($nominal) => [
                                    'kertas' => $nominal,
                                    'banyak_kertas' => 0,
                                    'sub_total_kertas' => 0,
                                ])->all())
                                ->schema([
                                    TextInput::make('kertas')
                                        ->label('Pecahan')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated(),

                                    TextInput::make('banyak_kertas')
                                        ->label('Jumlah Lembar')
                                        ->integer()
                                        ->minValue(0)
                                        ->default(0)
                                        ->live(debounce: 400)
                                        ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                            $set('sub_total_kertas', (int) ($get('kertas') ?? 0) * max(0, (int) $state));
                                        }),

                                    TextInput::make('sub_total_kertas')
                                        ->label('Subtotal')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->default(0)
                                        ->readOnly()
                                        ->dehydrated(),
                                ])
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::hitungKas($get, $set))
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->compact(),

                            TextInput::make('total_uang_kertas')
                                ->label('TOTAL UANG KERTAS')
                                ->prefix('Rp')
                                ->numeric()
                                ->default(0)
                                ->readOnly()
                                ->dehydrated(),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Informasi Saldo Kas')
                ->description('Bandingkan uang fisik dengan nominal pada sistem.')
                ->schema([
                    TextInput::make('total_sistem')
                        ->label('Total Uang Sistem')
                        ->prefix('Rp')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->live(debounce: 400)
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::hitungKas($get, $set)),

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
                        ->columnSpanFull(),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
