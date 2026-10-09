<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use ZeeshanTariq\FilamentStickyColumns\Concerns\InteractsWithStickyableColumns;

class ListTransactions extends ListRecords
{
    use InteractsWithStickyableColumns;
    
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->label('Tampilkan Semua'),
            'today_transaction' => Tab::make('Transaksi Hari Ini') 
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn() => $this->getModel()::whereDate('created_at', today())->count())
                ->icon(Heroicon::PlusCircle)
                ->badgeColor(Color::Red),
            'pengajuan_pembatalan' => Tab::make('Pengajuan Pembatalan') 
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TransactionStatus::PendingCancellation))
                ->badge(fn() => $this->getModel()::where('status', TransactionStatus::PendingCancellation)->count())
                ->icon(Heroicon::PlusCircle)
                ->badgeColor(Color::Red),
            'dibatalkan' => Tab::make('Dibatalkan') 
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TransactionStatus::Cancelled))
                ->badge(fn() => $this->getModel()::where('status', TransactionStatus::Cancelled)->count())
                ->icon(Heroicon::PlusCircle)
                ->badgeColor(Color::Red),
            
        ];
    }
}
