<?php

namespace App\Filament\Resources\TransactionCorrections;

use App\Filament\Resources\TransactionCorrections\Pages\CreateTransactionCorrection;
use App\Filament\Resources\TransactionCorrections\Pages\EditTransactionCorrection;
use App\Filament\Resources\TransactionCorrections\Pages\ListTransactionCorrections;
use App\Filament\Resources\TransactionCorrections\Pages\ViewTransactionCorrection;
use App\Filament\Resources\TransactionCorrections\Schemas\TransactionCorrectionForm;
use App\Filament\Resources\TransactionCorrections\Schemas\TransactionCorrectionInfolist;
use App\Filament\Resources\TransactionCorrections\Tables\TransactionCorrectionsTable;
use App\Models\TransactionCorrection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionCorrectionResource extends Resource
{
    protected static ?string $model = TransactionCorrection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionCorrectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionCorrectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionCorrectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionCorrections::route('/'),
            'create' => CreateTransactionCorrection::route('/create'),
            'view' => ViewTransactionCorrection::route('/{record}'),
            'edit' => EditTransactionCorrection::route('/{record}/edit'),
        ];
    }
}