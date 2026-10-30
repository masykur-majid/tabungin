<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('Nomor Rekening')
                    ->relationship(
                        name: 'account',
                        titleAttribute: 'nomor_rekening',
                        modifyQueryUsing: fn ($query) => $query->with('customer'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Account $record) => $record->nomor_rekening . ' - ' . ($record->customer?->nama ?? '-')
                    )
                    ->getSearchResultsUsing(function (string $search) {
                        return Account::query()
                            ->with('customer')
                            ->where(fn ($q) => $q
                                ->where('nomor_rekening', 'like', "%{$search}%")
                                ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$search}%")))
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($a) => [
                                $a->id => $a->nomor_rekening . ' - ' . ($a->customer?->nama ?? '-'),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('jumlah_transaksi')
                    ->label('Jumlah')
                    ->required()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(1000)
                    ->validationMessages([
                        'min' => 'Jumlah minimal Rp 1.000',
                    ]),

                Select::make('user_id')
                    ->label('Petugas')
                    ->relationship('user', 'name')
                    ->prefixIcon('heroicon-o-user')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('no_slip')
                    ->label('No. Slip')
                    ->placeholder('Otomatis')
                    ->disabled()
                    ->dehydrated(false),

                DatePicker::make('tanggal')
                    ->default(today())
                    ->disabled()
                    ->dehydrated(false),

                ToggleButtons::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->options([
                        'setoran' => 'Setoran',
                        'penarikan' => 'Penarikan',
                    ])
                    ->icons([
                        'setoran' => 'heroicon-m-arrow-down-tray',
                        'penarikan' => 'heroicon-m-arrow-up-tray',
                    ])
                    ->colors([
                        'setoran' => 'success',
                        'penarikan' => 'danger',
                    ])
                    ->grouped()
                    ->required()
                    ->default('setoran'),
            ]);
    }
}