<?php

namespace Modules\Finance\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('enrolment_id')
                    ->relationship('enrolment', 'id'),
                TextInput::make('application_id')
                    ->numeric(),
                Select::make('academic_session_id')
                    ->relationship('academicSession', 'name')
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('unpaid'),
                DatePicker::make('due_on'),
            ]);
    }
}
