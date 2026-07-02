<?php

namespace Modules\People\Filament\Resources\Enrolments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Modules\People\Models\Enrolment;

class EnrolmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matric_number')
                    ->label('Matric No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('person.surname')
                    ->label('Student')
                    ->formatStateUsing(fn ($record) => $record->person?->fullName())
                    ->searchable(['surname', 'first_name']),

                TextColumn::make('programme.name')
                    ->label('Programme')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('level.label')
                    ->label('Level')
                    ->sortable(),

                TextColumn::make('programme_type')
                    ->badge()
                    ->label('Type'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Enrolment::STATUS_ACTIVE => 'success',
                        Enrolment::STATUS_GRADUATED => 'info',
                        Enrolment::STATUS_WITHDRAWN => 'danger',
                        Enrolment::STATUS_DEFERRED => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('admissionSession.name')
                    ->label('Session')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('programme_type')
                    ->options([
                        Enrolment::TYPE_NCE => 'NCE',
                        Enrolment::TYPE_DEGREE => 'Degree',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        Enrolment::STATUS_ACTIVE => 'Active',
                        Enrolment::STATUS_GRADUATED => 'Graduated',
                        Enrolment::STATUS_WITHDRAWN => 'Withdrawn',
                        Enrolment::STATUS_DEFERRED => 'Deferred',
                    ]),
                \Filament\Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
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
