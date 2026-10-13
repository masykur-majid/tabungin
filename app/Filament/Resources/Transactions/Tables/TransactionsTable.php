<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.nomor_rekening')
                    ->label('No. Rekening')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_slip')
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('jenis_transaksi')
                    ->searchable(),
                TextColumn::make('jumlah_transaksi')
                    ->numeric()
                    ->sortable(),
               TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'sukses',
                        'warning' => 'pengajuan pembatalan',
                        'danger' => 'batal',
                    ])
                 ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                
            ])
           ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

                Action::make('Ajukan Pembatalan')
            ->color('warning')
            ->requiresConfirmation()
            ->form([
        
                Textarea::make('alasan_pembatalan')
            ->label('Alasan Pembatalan')
            ->required()
            ->placeholder('Masukkan alasan pembatalan transaksi...'),
            
            ])
            ->action(function ($record, array $data) {
        
        $record->update([
            'status' => 'pengajuan pembatalan',
            'alasan_pembatalan' => $data['alasan_pembatalan'], 
        ]);
    })
                    ->visible(fn ($record) => $record->status === 'sukses')
                    ->hidden(fn() => Filament::auth()->user()->hasRole('super_visor')),
              
              Action::make('Setujui Pembatalan')
                    ->color('danger')
                    ->requiresConfirmation()
                     ->form([
        
                Textarea::make('alasan_pembatalan')
                    ->label('Alasan Pembatalan')
                    ->required()
                    ->placeholder('Masukkan alasan pembatalan transaksi...')
                    ->default(fn ($record) => $record->alasan_pembatalan)
                    ->readOnly(),
            
            ])
                    ->action(function ($record) {
                        $record->update(['status' => 'batal']);

                        $account = $record->account;

                        if ($account) {
                            if ($record->jenis_transaksi === 'setoran') {
                                $account->decrement('saldo', $record->jumlah_transaksi);
                            } 

                            elseif ($record->jenis_transaksi === 'penarikan') {
                                $account->increment('saldo', $record->jumlah_transaksi);
                            }
                        }
                    })
                    ->visible(fn ($record) => $record->status === 'pengajuan pembatalan')
                    ->hidden(fn() => Filament::auth()->user()->hasRole('petugas')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
    