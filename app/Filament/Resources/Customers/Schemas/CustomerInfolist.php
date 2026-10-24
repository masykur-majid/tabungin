<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nik')
                ->label('NIK'),

                TextEntry::make('jenis_kelamin')
                ->label('Jenis kelamin')
                ->badge()
                ->color(fn (?string $state): string => match (true) {
                    str_contains(strtolower((string) $state), 'perempuan') => 'danger', 
                    str_contains(strtolower((string) $state), 'laki')      => 'info',  
                    default => 'gray',
                }),

                TextEntry::make('nama'),
                TextEntry::make('nisn'),
                TextEntry::make('asal_sekolah')
                ->label('Asal sekolah')
                ->badge()
                ->color(fn (?string $state): string => match (true) {
                    str_contains(strtolower((string) $state), 'smk') => 'warning', 
                    str_contains(strtolower((string) $state), 'smp') => 'info',
                    str_contains(strtolower((string) $state), 'guru') => 'success',
                    default => 'gray',
                }),
                
                TextEntry::make('alamat_rumah'),
                TextEntry::make('no_telepon'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
