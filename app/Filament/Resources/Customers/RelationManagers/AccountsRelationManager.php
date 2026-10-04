<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class AccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    protected static ?string $relatedResource = AccountResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label('Buka Rekening Baru')
                    ->icon(Heroicon::PlusCircle)
                    ->modalHeading('Buka Rekening Baru'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);;
    }
    
    public function form(Schema $schema): Schema
    {
        // Langsung gunakan skema form dari AccountResource
        return AccountResource::form($schema);
    }

   #[Override]
   public function isReadOnly(): bool
   {
    return false;
   }
}
