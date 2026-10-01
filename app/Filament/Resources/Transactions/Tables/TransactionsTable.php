<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('Setujui Pembatalan')
                    ->hidden(fn($record) => Filament::auth()->user()->hasRole('petugas') || $record->status != 'pengajuan pembatalan')
                    ->icon(Heroicon::Check)
                    ->color(Color::Green)
                    ->action(function (Transaction $transaction){
                        
                        $transaction->update(['status' => 'dibatalkan']);
                        $account = $transaction->account;
                        if($account){
                            $account->decrement('saldo', $transaction->jumlah_transaksi);
                            Notification::make()
                            ->title('Pembatalan Transaksi Berhasil!')
                            ->success()
                            ->send();
                        }else{
                             Notification::make()
                                ->title('terjadi kesalahan sistem')
                                ->success()
                                ->send();
                        }

                        
                    }),
                Action::make('Ajukan Pembatalan')
                    ->hidden(fn($record): bool => Filament::auth()->user()->hasRole('super_admin') || $record->status == 'pengajuan pembatalan' || $record->status == 'dibatalkan')
                    ->icon(Heroicon::XCircle)
                    ->color(Color::Red)
                    ->action(function (Transaction $transaction){
                        
                        $transaction->update(['status' => 'pengajuan pembatalan']);

                        Notification::make()
                            ->title('Pengajuan Pembatalan Telah Dikirim!')
                            ->success()
                            ->send();
                    }),
               

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
