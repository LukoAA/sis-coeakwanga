<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Services\AdmissionService;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\PaymentService;
use Modules\People\Models\Person;
use Modules\People\Models\Enrolment;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('applicant_surname')
                    ->label('Applicant')
                    ->formatStateUsing(fn ($record) => trim("{$record->applicant_surname} {$record->applicant_first_name}"))
                    ->searchable(['applicant_surname', 'applicant_first_name'])
                    ->sortable(),
                TextColumn::make('programme.name')->label('Programme')->toggleable(),
                TextColumn::make('entry_route')->badge()->label('Route'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Application::STATUS_PENDING => 'gray',
                        Application::STATUS_SCREENED => 'info',
                        Application::STATUS_OFFERED => 'warning',
                        Application::STATUS_ACCEPTED => 'primary',
                        Application::STATUS_ENROLLED => 'success',
                        Application::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('acceptance_fee_paid')
                    ->label('Fee paid')
                    ->state(fn (Application $record) => static::acceptanceFeePaid($record))
                    ->boolean(),
                TextColumn::make('screening_score')->label('Score')->toggleable(),
                TextColumn::make('academicSession.name')->label('Session')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Application::STATUS_PENDING => 'Pending',
                        Application::STATUS_SCREENED => 'Screened',
                        Application::STATUS_OFFERED => 'Offered',
                        Application::STATUS_ACCEPTED => 'Accepted',
                        Application::STATUS_ENROLLED => 'Enrolled',
                        Application::STATUS_REJECTED => 'Rejected',
                    ]),
                SelectFilter::make('entry_route')
                    ->options([
                        Enrolment::ROUTE_UTME => 'UTME',
                        Enrolment::ROUTE_DIRECT_ENTRY => 'Direct Entry',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                // 1. SCREEN — pending -> screened
                Action::make('screen')
                    ->label('Screen')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->visible(fn (Application $record) => $record->status === Application::STATUS_PENDING)
                    ->schema([
                        TextInput::make('screening_score')
                            ->label('Screening score')
                            ->numeric()->minValue(0)->maxValue(100)->required(),
                    ])
                    ->action(function (array $data, Application $record) {
                        app(AdmissionService::class)->screen($record, (float) $data['screening_score']);
                        Notification::make()->title('Application screened')->success()->send();
                    }),

                // 2. MAKE OFFER — screened -> offered
                Action::make('makeOffer')
                    ->label('Make offer')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Send an offer of admission to this applicant?')
                    ->visible(fn (Application $record) => $record->status === Application::STATUS_SCREENED)
                    ->action(function (Application $record) {
                        app(AdmissionService::class)->makeOffer($record);
                        Notification::make()->title('Offer made')->success()->send();
                    }),

                // 3. ACCEPT OFFER — offered -> accepted, AND auto-generate the acceptance invoice
                Action::make('acceptOffer')
                    ->label('Accept offer')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalDescription('Record the applicant\'s acceptance? This generates their acceptance-fee invoice.')
                    ->visible(fn (Application $record) => $record->status === Application::STATUS_OFFERED)
                    ->action(function (Application $record) {
                        $service = app(AdmissionService::class);
                        if ($record->offer) {
                            $service->acceptOffer($record->offer);
                        } else {
                            $record->update(['status' => Application::STATUS_ACCEPTED]);
                        }
                        // Auto-generate the acceptance invoice so the bursary can collect it.
                        app(InvoiceGenerator::class)->generateAcceptanceInvoice($record);
                        Notification::make()
                            ->title('Offer accepted')
                            ->body('Acceptance-fee invoice generated.')
                            ->success()->send();
                    }),

               
                // 4. FINALISE — accepted & fee paid -> enrolled (matcher + matric + enrolment)
                Action::make('finalise')
                    ->label('Finalise admission')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (Application $record) => $record->status === Application::STATUS_ACCEPTED
                        && static::acceptanceFeePaid($record))
                    ->schema(fn (Application $record) => [
                        Radio::make('person_choice')
                            ->label('Applicant identity')
                            ->options(static::matchOptions($record))
                            ->default(static::defaultChoice($record))
                            ->required()
                            ->helperText('If this applicant matches an existing person (e.g. a returning NCE graduate), link them so a second enrolment is added to the same record.'),
                    ])
                    ->action(function (array $data, Application $record) {
                        $service = app(AdmissionService::class);
                        $existingPerson = $data['person_choice'] !== 'new'
                            ? Person::find((int) $data['person_choice'])
                            : null;

                        try {
                            $enrolment = $service->finaliseAdmission($record, $existingPerson);
                            Notification::make()
                                ->title('Admission finalised')
                                ->body("Matric number: {$enrolment->matric_number}")
                                ->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Could not finalise')->body($e->getMessage())->danger()->send();
                        }
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    protected static function acceptanceFeePaid(Application $record): bool
    {
        $invoice = Invoice::where('application_id', $record->id)->first();

        return $invoice ? $invoice->percentPaid() >= 100.0 : false;
    }

    protected static function matchOptions(Application $record): array
    {
        $options = [];
        foreach (app(AdmissionService::class)->findReturningPersonMatches($record) as $match) {
            $person = $match['person'];
            $reasons = implode(', ', $match['reasons']);
            $options[(string) $person->id] = "{$person->fullName()} — match score {$match['score']} ({$reasons})";
        }
        $options['new'] = 'Create a NEW person (no existing match)';

        return $options;
    }

    protected static function defaultChoice(Application $record): string
    {
        $matches = app(AdmissionService::class)->findReturningPersonMatches($record);

        return $matches->isNotEmpty() ? (string) $matches->first()['person']->id : 'new';
    }
}