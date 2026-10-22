<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TransaksiHariIniTable extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini (Minimal 10 Transaksi)';

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->take(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('account.nomor_rekening')->label('No. Rekening'),
                Tables\Columns\TextColumn::make('user.name')->label('Petugas'),
                Tables\Columns\TextColumn::make('jenis_transaksi'),
                // Kolom jumlah_transaksi dihilangkan demi privasi
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->time()->label('Jam'),
            ])
            ->paginated(false);
    }
}