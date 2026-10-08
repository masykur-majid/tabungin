<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Schemas\TransactionInfolist;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    
    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';
    
    protected static ?string $modelLabel = 'Transaksi';

    protected static string|BackedEnum|null $navigationIcon = 'fas-money-bill-transfer';

    protected static ?int $navigationSort = 3;

    #[Override]
    public static function getNavigationBadge(): ?string
    {
         $trx =  static::getModel()::whereDate('created_at', today())->count();

        return (string) $trx;
    }
    
    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view' => ViewTransaction::route('/{record}'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }

     #[Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        $query = parent::getEloquentQuery();
        if($user->hasRole('petugas')){
            $query->where('user_id', $user->id);
        }
        return $query;
    }
}
