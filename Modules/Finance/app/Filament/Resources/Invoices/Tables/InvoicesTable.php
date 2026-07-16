<?php

namespace Modules\Finance\Filament\Resources\Invoices\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\PaymentService;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Invoice')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),

                TextColumn::make('payer')
                    ->label('For')
                    ->state(fn (Invoice $record) => static::payerLabel($record))
                    ->searchable(false),

                TextColumn::make('academicSession.name')
                    ->label('Session')
                    ->toggleable(),

                TextColumn::make('total')
                    ->money('NGN')
                    ->sortable(),

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
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Invoice::STATUS_UNPAID => 'Unpaid',
                        Invoice::STATUS_PART => 'Part-paid',
                        Invoice::STATUS_PAID => 'Paid',
                    ]),
            ])
            ->recordActions([
                Action::make('recordPayment')
                    ->label('Record payment')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->balance() > 0)
                    ->schema([
                        TextInput::make('amount')
                            ->label('Amount received (NGN)')
                            ->numeric()
                            ->minValue(0.01)
                            ->required()
                            ->default(fn (Invoice $record) => $record->balance())
                            ->helperText(fn (Invoice $record) => 'Outstanding balance: ₦'.number_format($record->balance(), 2)),
                    ])
                    ->action(function (array $data, Invoice $record) {
                        $payments = app(PaymentService::class);
                        $payments->confirmPayment($payments->recordPayment($record, (float) $data['amount']));

                        Notification::make()
                            ->title('Payment recorded')
                            ->body('Invoice balance is now ₦'.number_format($record->fresh()->balance(), 2))
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Who the invoice is for — a student (enrolment) or an applicant. */
    protected static function payerLabel(Invoice $record): string
    {
        if ($record->enrolment_id && $record->enrolment) {
            return $record->enrolment->matric_number
                ?? ('Enrolment #'.$record->enrolment_id);
        }

        if ($record->application_id) {
            return 'Applicant (app #'.$record->application_id.')';
        }

        return '—';
    }
}