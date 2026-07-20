<?php

namespace App\Filament\Student\Resources\Invoices\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Finance\Models\Invoice;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicSession.name')->label('Session'),
                TextColumn::make('total')->money('NGN'),
                TextColumn::make('paid')
                    ->label('Paid')
                    ->state(fn (Invoice $record) => $record->confirmedPaymentsTotal())
                    ->money('NGN'),
                TextColumn::make('balance')
                    ->label('Balance')
                    ->state(fn (Invoice $record) => $record->balance())
                    ->money('NGN')
                    ->color(fn (Invoice $record) => $record->balance() > 0 ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Invoice::STATUS_PAID => 'success',
                        Invoice::STATUS_PART => 'warning',
                        Invoice::STATUS_UNPAID => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([]);
    }
}