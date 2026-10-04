<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Account;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\RawJs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->displayFormat('d F Y')
                    ->native(false)
                    ->locale('id')
                    ->dehydrated(false)
                    ->required()
                    ->default(today())
                    ->maxDate(today())
                    ->minDate(today())
                    ->prefixIcon(Heroicon::Calendar)
                    ->readOnly(),

                 TextInput::make('user_id')
                    ->label('Petugas')
                    ->prefixIcon('heroicon-o-user')
                    ->default(fn() => auth()->user()?->name)
                    ->readOnly()
                    ->dehydrated()
                    ->dehydrateStateUsing(fn() => auth()->id()),

                Select::make('account_id')
                    ->label('Nomor Rekening')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(fn() => Account::where('is_active', true)
                                        ->with('customer')
                                        ->get()
                                        ->mapWithKeys(fn(Account $account) => [$account->id => $account->accountLabel])
                    )
                    ->getOptionLabelUsing(fn ($value) => Account::with('customer')->find($value)?->accountLabel ?? '[rekening tidak ditemukan]'),

               

                TextInput::make('no_slip')
                    ->label('No Slip')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(false),


                TextInput::make('jenis_transaksi')
                    ->required()
                    ->default('setoran'),

                TextInput::make('jumlah_transaksi')
                    ->required()
                    ->prefix('Rp') 
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                    
            ]);
    }
}