<?php

namespace App\Filament\Resources\Closings\Pages;

use App\Filament\Resources\Closings\ClosingResource;
use App\Models\Closing;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClosings extends ListRecords
{
    protected static string $resource = ClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(
                    fn (): bool => Closing::query()
                        ->whereDate('created_at', today())
                        ->exists()
                ),
        ];
    }
}
