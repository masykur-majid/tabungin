<?php

namespace App\Filament\Resources\Closings\Schemas;

use App\Models\Transaction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ClosingForm
{

    private static function totalTransaksi(?string $tanggal): int
    {
        if (! $tanggal) {
            return 0;
        }

        return (int) Transaction::query()
            ->whereDate('tanggal', $tanggal)
            ->where('jenis_transaksi', 'setoran')
            ->sum('jumlah_transaksi');
    }

    private static function hitungKas(
        Get $get,
        Set $set
    ): void {
        $totalKoin = 0;

        foreach (($get('jumlah_uang_koin') ?? []) as $item) {
            $pecahan = (int) ($item['koin'] ?? 0);

            $jumlah = max(
                0,
                (int) ($item['banyak_koin'] ?? 0)
            );

            $totalKoin += $pecahan * $jumlah;
        }

        $totalKertas = 0;

        foreach (($get('jumlah_uang_kertas') ?? []) as $item) {
            $pecahan = (int) ($item['kertas'] ?? 0);

            $jumlah = max(
                0,
                (int) ($item['banyak_kertas'] ?? 0)
            );

            $totalKertas += $pecahan * $jumlah;
        }

        $totalFisik = $totalKoin + $totalKertas;

        $totalSistem = (int) (
            $get('total_sistem') ?? 0
        );

        $selisih = $totalFisik - $totalSistem;

        $set('total_uang_koin', $totalKoin);
        $set('total_uang_kertas', $totalKertas);
        $set('total_fisik', $totalFisik);
        $set('selisih', $selisih);
    }

    public static function configure(Schema $schema): Schema
    {
        $defaultKoin = collect([
            100,
            200,
            500,
            1000,
        ])
            ->map(fn ($nominal) => [
                'koin' => $nominal,
                'banyak_koin' => 0,
                'sub_total_koin' => 0,
            ])
            ->all();

        $defaultKertas = collect([
            1000,
            2000,
            5000,
            10000,
            20000,
            50000,
            100000,
        ])
            ->map(fn ($nominal) => [
                'kertas' => $nominal,
                'banyak_kertas' => 0,
                'sub_total_kertas' => 0,
            ])
            ->all();

        return $schema->components([


            Section::make('Informasi Tutup Kas')
                ->description(
                    'Informasi waktu dan petugas yang melakukan tutup kas.'
                )
                ->schema([

                    TextInput::make('created_at')
                        ->label('Tanggal Dibuat')
                        ->formatStateUsing(function ($record) {
                            return $record?->created_at
                                ? $record->created_at->format(
                                    'd/m/Y H:i:s'
                                )
                                : now()->format(
                                    'd/m/Y H:i:s'
                                );
                        })
                        ->readOnly()
                        ->dehydrated(false),

                    TextInput::make('updated_at')
                        ->label('Terakhir Diubah')
                        ->formatStateUsing(function ($record) {
                            return $record?->updated_at
                                ? $record->updated_at->format(
                                    'd/m/Y H:i:s'
                                )
                                : '-';
                        })
                        ->readOnly()
                        ->dehydrated(false),

                    TextInput::make('user_id')
                        ->label('Nama Petugas')
                        ->formatStateUsing(
                            fn () => Auth::user()?->name
                        )
                        ->readOnly()
                        ->dehydrated(false),
                ])
                ->columns(3)
                ->columnSpanFull(),


            Section::make('Perhitungan Uang Fisik')
                ->description(
                    'Masukkan jumlah keping atau lembar pada setiap pecahan.'
                )
                ->schema([


                    Section::make('Uang Koin')
                        ->description(
                            'Masukkan jumlah keping sesuai pecahan.'
                        )
                        ->schema([

                            Repeater::make('jumlah_uang_koin')
                                ->label('Rincian Uang Koin')
                                ->table([
                                    TableColumn::make('Pecahan'),
                                    TableColumn::make('Jumlah Keping'),
                                    TableColumn::make('Subtotal'),
                                ])
                                ->default($defaultKoin)
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
                                        ->extraInputAttributes([
                                            'onfocus' => 'this.select()',
                                        ])
                                        ->afterStateUpdated(
                                            function (
                                                Get $get,
                                                Set $set,
                                                $state
                                            ): void {

                                                $pecahan = (int) (
                                                    $get('koin') ?? 0
                                                );

                                                $jumlah = max(
                                                    0,
                                                    (int) $state
                                                );

                                                $set(
                                                    'sub_total_koin',
                                                    $pecahan * $jumlah
                                                );
                                            }
                                        ),

                                    TextInput::make('sub_total_koin')
                                        ->label('Subtotal')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->default(0)
                                        ->readOnly()
                                        ->dehydrated(),
                                ])
                                ->live()
                                ->afterStateUpdated(
                                    fn (
                                        Get $get,
                                        Set $set
                                    ) => self::hitungKas(
                                        $get,
                                        $set
                                    )
                                )
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
                        ->description(
                            'Masukkan jumlah lembar sesuai pecahan.'
                        )
                        ->schema([

                            Repeater::make('jumlah_uang_kertas')
                                ->label('Rincian Uang Kertas')
                                ->table([
                                    TableColumn::make('Pecahan'),
                                    TableColumn::make('Jumlah Lembar'),
                                    TableColumn::make('Subtotal'),
                                ])
                                ->default($defaultKertas)
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
                                        ->extraInputAttributes([
                                            'onfocus' => 'this.select()',
                                        ])
                                        ->afterStateUpdated(
                                            function (
                                                Get $get,
                                                Set $set,
                                                $state
                                            ): void {

                                                $pecahan = (int) (
                                                    $get('kertas') ?? 0
                                                );

                                                $jumlah = max(
                                                    0,
                                                    (int) $state
                                                );

                                                $set(
                                                    'sub_total_kertas',
                                                    $pecahan * $jumlah
                                                );
                                            }
                                        ),

                                    TextInput::make(
                                        'sub_total_kertas'
                                    )
                                        ->label('Subtotal')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->default(0)
                                        ->readOnly()
                                        ->dehydrated(),
                                ])
                                ->live()
                                ->afterStateUpdated(
                                    fn (
                                        Get $get,
                                        Set $set
                                    ) => self::hitungKas(
                                        $get,
                                        $set
                                    )
                                )
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
                ->description(
                    'Perbandingan antara total uang sistem dengan uang fisik.'
                )
                ->schema([

                    TextInput::make('total_sistem')
                        ->label('Total Uang Sistem')
                        ->helperText(
                            'Otomatis mengambil total transaksi setoran pada hari ini.'
                        )
                        ->prefix('Rp')
                        ->numeric()
                        ->default(0)
                        ->readOnly()
                        ->dehydrated()
                        ->afterStateHydrated(
                            function (
                                Get $get,
                                Set $set,
                                $record
                            ): void {

                                $tanggal = $record?->created_at
                                    ?->toDateString()
                                    ?? today()->toDateString();

                                $totalSistem =
                                    self::totalTransaksi(
                                        $tanggal
                                    );

                                $set(
                                    'total_sistem',
                                    $totalSistem
                                );

                                self::hitungKas(
                                    $get,
                                    $set
                                );
                            }
                        )
                        ->required(),

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

                    Placeholder::make('peringatan_kas')
                        ->label('Status Pemeriksaan Kas')
                        ->content(function (
                            Get $get
                        ): string {

                            $selisih = (int) (
                                $get('selisih') ?? 0
                            );

                            if ($selisih === 0) {
                                return
                                    '✅ KAS SESUAI — '
                                    . 'Uang fisik sudah sesuai '
                                    . 'dengan total uang sistem. '
                                    . 'Tutup kas dapat diajukan.';
                            }

                            return
                                '⚠️ TERDAPAT SELISIH KAS — '
                                . 'Uang fisik berbeda dengan '
                                . 'total uang sistem. '
                                . 'Silakan periksa kembali '
                                . 'jumlah uang.';
                        })
                        ->live()
                        ->columnSpanFull(),

                    Hidden::make('status')
                        ->default('Buka')
                        ->dehydrated(),

                    Textarea::make('catatan')
                        ->label('Catatan')
                        ->placeholder(
                            'Masukkan catatan tutup kas'
                        )
                        ->default('-')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}