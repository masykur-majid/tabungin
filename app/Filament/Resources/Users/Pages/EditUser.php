<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    #[Override]
    public function getHeading(): string|Htmlable|null
    {
        return '';
    }
    
    #[Override]
    protected function getFormActions(): array
    {
        return [];
    }

    public function getMaxContentWidth(): ?string
    {
        return '2xl';
    }

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
