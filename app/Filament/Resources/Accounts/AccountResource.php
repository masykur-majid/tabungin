<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\Accounts\Schemas\AccountForm;
use App\Filament\Resources\Accounts\Schemas\AccountInfolist;
use App\Filament\Resources\Accounts\Tables\AccountsTable;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccountResource extends Resource
{   
    protected static ?string $model = Account::class;
    protected static ?string $navigationLabel = 'Rekening';
    protected static ?string $pluralModelLabel = 'Rekening';
    protected static ?string $modelLabel = 'Rekening';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;
    
    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,

        ];
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListAccounts::route('/'),
        //'create' => Pages\CreateAccount::route('/create'),
        //'edit' => Pages\EditAccount::route('/{record}/edit'),
    ];
}
}
