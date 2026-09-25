<?php

namespace App\Filament\Resources\Closings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('total_sistem')
                    ->numeric(),
                TextEntry::make('total_fisik')
                    ->numeric(),
                TextEntry::make('selisih')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('catatan')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
