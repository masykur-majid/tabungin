<?php

namespace App\Filament\Resources\Closings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('created_at')
                ->label('Tanggal Dibuat')
                ->dateTime('d/m/Y H:i:s')
                ->placeholder('-'),

            TextEntry::make('total_sistem')
                ->label('Total Uang Sistem')
                ->numeric(),

            TextEntry::make('total_fisik')
                ->label('Total Uang Fisik')
                ->numeric(),

            TextEntry::make('selisih')
                ->label('Selisih Kas')
                ->numeric(),

            TextEntry::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'pengajuan_tutup' => 'Pengajuan Tutup',
                    'tutup' => 'Tutup',
                    default => $state,
                })
                ->badge(),
                
            TextEntry::make('is_approved')
                ->label('Persetujuan')
                ->formatStateUsing(fn ($state) =>
                    $state ? 'Disetujui' : 'Belum disetujui'
                )
                ->badge(),

            TextEntry::make('catatan')
                ->label('Catatan')
                ->placeholder('-')
                ->columnSpanFull(),

            TextEntry::make('updated_at')
                ->label('Terakhir Diubah')
                ->dateTime('d/m/Y H:i:s')
                ->placeholder('-'),
        ]);
    }
}
