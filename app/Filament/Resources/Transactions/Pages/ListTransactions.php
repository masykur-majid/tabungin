<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
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
            
            'pengajuan_pembatalan' => Tab::make('Pengajuan Pembatalan') 
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pengajuan pembatalan'))
                ->badge($this->getModel()::where('status', 'pengajuan pembatalan')->count())
                ->icon(Heroicon::PlusCircle)
                ->badgeColor(Color::Red),
        ];
    }
}
