<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->label('No. Slip')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jenis_transaksi')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state): string => match (strtolower($state)) {
                        'setoran' => 'success',
                        'penarikan' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'sukses' => 'success',
                        'pengajuan pembatalan' => 'warning',
                        'batal', 'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                ->date('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),

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

                ActionGroup::make([
                    EditAction::make()
                          ->hidden(fn (Transaction $record): bool => in_array($record->status, ['sukses', 'pengajuan pembatalan', 'dibatalkan', 'batal'])),

                    Action::make('Ajukan Pembatalan')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('alasan_batal')
                                ->label('Alasan Pembatalan')
                                ->placeholder('Masukkan alasan pembatalan transaksi...')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'pengajuan pembatalan',
                                'alasan_batal' => $data['alasan_batal'],
                                'tanggal_pengajuan_batal' => now(),
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
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Pembatalan Transaksi')
                        ->modalDescription(fn ($record) => 'Alasan: ' . $record->alasan_batal)
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'dibatalkan',
                                'tanggal_dibatalkan' => now(),
                                'approver' => Filament::auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Pembatalan transaksi disetujui')
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => $record->status === TransactionStatus::PendingCancellation->value
                            && ! Filament::auth()->user()->hasRole('petugas')),

                    DeleteAction::make()
                        ->visible(fn (): bool => Filament::auth()->user()->hasAnyRole(['admin', 'super_admin'])),
                ]),
            ])
                ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Filament::auth()->user()->hasAnyRole(['admin', 'super_admin'])),
                ]),
            ]);
    }
} 