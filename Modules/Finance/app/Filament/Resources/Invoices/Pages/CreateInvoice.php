<?php

namespace Modules\Finance\Filament\Resources\Invoices\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\Invoices\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
