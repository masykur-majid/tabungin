<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.nomor_rekening')
                    ->label('No. Rekening')
                    ->description(fn ($record) => $record->account?->customer?->nama)
                    ->fontFamily('mono')
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('account', function ($q) use ($search) {
                            $q->where('nomor_rekening', 'like', "%{$search}%")
                                ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$search}%"));
                        });
                    })
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
                    ->color(fn (string $state): string => match ($state) {
                        'sukses' => 'success',
                        'pengajuan_pembatalan' => 'warning',
                        'batal' => 'danger',
                        default => 'gray',
                    }),

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
                ViewAction::make()->iconButton(),

                EditAction::make()
                    ->iconButton()
                    ->hidden(fn (Transaction $record): bool => $record->status === 'sukses'),

                Action::make('Ajukan Pembatalan')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->tooltip('Ajukan Pembatalan')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('alasan_pembatalan')
                            ->label('Alasan Pembatalan')
                            ->placeholder('Masukkan alasan pembatalan transaksi...')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'pengajuan_pembatalan',
                            'alasan_pembatalan' => $data['alasan_pembatalan'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan pembatalan berhasil dikirim')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'sukses'
                        && ! Filament::auth()->user()->hasRole('super_visor')),

                Action::make('Setujui Pembatalan')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->tooltip('Setujui Pembatalan')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembatalan Transaksi')
                    ->modalDescription(fn ($record) => 'Alasan: ' . $record->alasan_pembatalan)
                    ->action(function ($record) {
                        $account = $record->account;

                        if ($record->jenis_transaksi === 'penarikan') {
                            $account->increment('saldo', $record->jumlah_transaksi);
                        } else {
                            $account->decrement('saldo', $record->jumlah_transaksi);
                        }

                        $record->update(['status' => 'batal']);

                        Notification::make()
                            ->title('Pembatalan transaksi disetujui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === TransactionStatus::PendingCancellation->value
                        && ! Filament::auth()->user()->hasRole('petugas')),

                Action::make('cetak')
                    ->icon('heroicon-o-printer')
                    ->iconButton()
                    ->tooltip('Bukti Transaksi')
                    ->modalHeading('Bukti Transaksi')
                    ->modalWidth('md')
                    ->modalContent(fn ($record) => view('filament.bukti-transaksi-modal', [
                        'transaction' => $record->load(['account.customer', 'user']),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraModalFooterActions(fn ($record) => [
                        Action::make('cetakBukti')
                            ->label('Cetak')
                            ->icon('heroicon-o-printer')
                            ->url(route('transaksi.bukti', $record))
                            ->openUrlInNewTab(),
                    ]),

                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}