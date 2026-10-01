<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\TransactionCorrection;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
    ->label('Status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'sukses' => 'success',
        'pending' => 'warning',
        'batal' => 'danger',
        default => 'gray',
    })
    ->formatStateUsing(fn (string $state): string => match ($state) {
        'sukses' => 'Sukses',
        'pending' => 'Pending',
        'batal' => 'Batal',
        default => ucfirst($state),
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

                Action::make('ajukanKoreksi')
                    ->label('Ajukan Koreksi')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan Kesalahan')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function ($record, array $data) {
                        TransactionCorrection::create([
                            'transaction_id' => $record->id,
                            'user_id' => auth()->id(),
                            'alasan' => $data['alasan'],
                            'status' => 'pending',
                        ]);
                    })
                    ->successNotificationTitle('Pengajuan koreksi berhasil dikirim'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}