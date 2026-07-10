<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Services\AdmissionService;
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
                IconColumn::make('acceptance_fee_paid')->label('Fee paid')->boolean(),
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
                Action::make('screen')
                    ->label('Screen')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->visible(fn (Application $record) => $record->status === Application::STATUS_PENDING)
                    ->schema([
                        TextInput::make('screening_score')
                            ->label('Screening score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ])
                    ->action(function (array $data, Application $record) {
                        app(AdmissionService::class)->screen($record, (float) $data['screening_score']);
                        Notification::make()->title('Application screened')->success()->send();
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
}