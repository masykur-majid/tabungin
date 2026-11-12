<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('account.nomor_rekening')
                    ->label('No. Rekening'),

                TextEntry::make('user.name')
                    ->label('Petugas'),

                TextEntry::make('no_slip')
                    ->label('No. Slip'),

                TextEntry::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('jenis_transaksi')
                    ->label('Jenis Transaksi'),

                TextEntry::make('jumlah_transaksi')
                    ->label('Jumlah Transaksi')
                    ->numeric(),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sukses' => 'success',
                        'pengajuan pembatalan' => 'warning',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('alasan_batal')
                    ->label('Alasan Pembatalan')
                    ->visible(fn ($record) => filled($record->alasan_batal)),

                TextEntry::make('tanggal_pengajuan_batal')
                    ->label('Tanggal Pengajuan Batal')
                    ->dateTime('d/m/Y H:i')
                    ->visible(fn ($record) => filled($record->tanggal_pengajuan_batal)),

                TextEntry::make('tanggal_dibatalkan')
                    ->label('Tanggal Dibatalkan')
                    ->dateTime('d/m/Y H:i')
                    ->visible(fn ($record) => filled($record->tanggal_dibatalkan)),

                TextEntry::make('approverUser.name')
                    ->label('Disetujui Oleh')
                    ->visible(fn ($record) => filled($record->approver)),

                TextEntry::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}