<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Override;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    // #[Override]
    // public function getMaxContentWidth(): Width|string|null
    // {
    //     return '2xl';
    // }
}
