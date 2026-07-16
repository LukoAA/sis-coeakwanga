<?php

namespace Modules\Finance\Filament\Resources\Invoices;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\Invoices\Pages\CreateInvoice;
use Modules\Finance\Filament\Resources\Invoices\Pages\EditInvoice;
use Modules\Finance\Filament\Resources\Invoices\Pages\ListInvoices;
use Modules\Finance\Filament\Resources\Invoices\Pages\ViewInvoice;
use Modules\Finance\Filament\Resources\Invoices\Schemas\InvoiceForm;
use Modules\Finance\Filament\Resources\Invoices\Schemas\InvoiceInfolist;
use Modules\Finance\Filament\Resources\Invoices\Tables\InvoicesTable;
use Modules\Finance\Models\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
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
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
