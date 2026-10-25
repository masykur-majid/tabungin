<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class PendingCancellationTable extends BaseWidget
{
    protected static ?string $heading = 'Pengajuan Pembatalan Perlu Di-Resolve';

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->where('status', 'pengajuan pembatalan')
            )
            ->columns([
                Tables\Columns\TextColumn::make('account.nomor_rekening')->label('No. Rekening'),
                Tables\Columns\TextColumn::make('user.name')->label('Petugas'),
                Tables\Columns\TextColumn::make('jenis_transaksi'),
                Tables\Columns\TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah Transaksi')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('alasan_pembatalan')->label('Alasan'),
            ])
            ->actions([
                Action::make('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $account = $record->account;
                            if ($account) {
                                if ($record->jenis_transaksi === 'penarikan') {
                                    $account->increment('saldo', $record->jumlah_transaksi);
                                } elseif ($record->jenis_transaksi === 'setoran') {
                                    $account->decrement('saldo', $record->jumlah_transaksi);
                                }
                            }
                            $record->update(['status' => 'batal']);
                        });
                    }),

                Action::make('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'sukses'])),
            ]);
    }
}