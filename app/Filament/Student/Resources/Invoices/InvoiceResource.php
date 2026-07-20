<?php

namespace App\Filament\Student\Resources\Invoices;

use App\Filament\Student\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Student\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Student\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Student\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Student\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Student\Resources\Invoices\Schemas\InvoiceInfolist;
use App\Filament\Student\Resources\Invoices\Tables\InvoicesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
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

    protected static ?string $navigationLabel = 'My Fees';

    protected static ?string $modelLabel = 'invoice';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $enrolmentIds = \Modules\People\Models\Enrolment::where(
            'person_id', auth()->user()?->person_id ?? 0
        )->pluck('id');

        return parent::getEloquentQuery()->whereIn('enrolment_id', $enrolmentIds);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }
}
