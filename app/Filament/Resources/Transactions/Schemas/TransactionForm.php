<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Config;
use Riskihajar\Terbilang\Facades\Terbilang;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
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
                        
                        ToggleButtons::make('jenis_transaksi')
                            ->options([
                                'setoran' => 'Setoran',
                                'penarikan' => 'Penarikan',
                            ])
                            ->colors([
                                'setoran' => Color::Green,
                                'penarikan' => 'warning'
                            ])
                            ->default('setoran')
                            ->inline()
                            ->grouped()
                            ->required(),

                        Select::make('account_id')
                            ->label('Nomor Rekening')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => 'fira-code'
                            ])
            
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Account::where('is_active', true)
                                                ->with('customer')
                                                ->get()
                                                ->mapWithKeys(fn(Account $account) => [$account->id => $account->accountLabel])
                            )
                            ->getOptionLabelUsing(fn ($value) => Account::with('customer')->find($value)?->accountLabel ?? '[rekening tidak ditemukan]'),

                        

                        TextInput::make('jumlah_transaksi')
                            ->required()
                            ->columnSpan(2)
                            ->prefix('Rp') 
                            ->mask(RawJs::make('$money($input, \',\')'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->extraAttributes([
                                'class' => 'big-display margin-nol'
                            ])
                            ->extraInputAttributes(['style' => 'text-align: right; font-size:1.7rem;'])
                            ->placeholder(0)
                            ->live()
                            ->afterStateUpdated(function($state, $set){
                                Config::set('terbilang.locale', 'id');
                                if(empty($state)){
                                    $set('terbilang', 'nol rupiah');
                                }else{
                                    $set('terbilang', ucfirst(Terbilang::make($state).' rupiah'));
                                }
                                
                            }),
                        
                        TextEntry::make('terbilang')
                            ->label('Terbilang:')
                            ->view('filament.forms.components.inline-terbilang')
                            ->columnSpan(3)
                            ->extraAttributes([
                                'class' => 'inline-rapat'
                            ])

                    ])
                    ->columns(3)
            ])
            ->columns(1);
    }
}