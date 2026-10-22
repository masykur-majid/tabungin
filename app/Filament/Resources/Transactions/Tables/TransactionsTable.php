<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
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
                //
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),

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

                Action::make('Ajukan Pembatalan')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->tooltip('Ajukan Pembatalan')
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

                        \Filament\Notifications\Notification::make()
                            ->title('Pengajuan pembatalan berhasil dikirim')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'sukses')
                    ->hidden(fn () => Filament::auth()->user()->hasRole('super_visor')),

                Action::make('Setujui Pembatalan')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->tooltip('Setujui Pembatalan')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembatalan Transaksi')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pembatalan ini? Saldo rekening nasabah akan disesuaikan secara otomatis.')
                    ->visible(fn ($record) => $record->status === 'pengajuan pembatalan')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            // Menggunakan relasi Eloquent account
                            $account = $record->account;

                            if ($account) {
                                // Penyesuaian saldo berdasarkan jenis transaksi
                                if ($record->jenis_transaksi === 'penarikan') {
                                    // Pembatalan penarikan = Saldo dikembalikan (ditambah)
                                    $account->increment('saldo', $record->jumlah_transaksi);
                                } elseif ($record->jenis_transaksi === 'setoran') {
                                    // Pembatalan setoran = Saldo ditarik kembali (dikurangi)
                                    $account->decrement('saldo', $record->jumlah_transaksi);
                                }
                            }

                            // Ubah status transaksi menjadi 'batal' (sesuai ENUM database)
                            $record->update([
                                'status' => 'batal',
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Pembatalan transaksi berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('Tolak Pembatalan')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->tooltip('Tolak Pembatalan')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembatalan Transaksi')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan pembatalan ini? Status transaksi akan kembali ke "sukses".')
                    ->visible(fn ($record) => $record->status === 'pengajuan pembatalan')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'sukses',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Pengajuan pembatalan telah ditolak')
                            ->warning()
                            ->send();
                    }),

                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}