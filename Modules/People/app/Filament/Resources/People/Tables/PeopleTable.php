<?php

namespace Modules\People\Filament\Resources\People\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PeopleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('surname')
                    ->label('Name')
                    ->formatStateUsing(fn ($record) => $record->fullName())
                    ->searchable(['surname', 'first_name', 'other_names'])
                    ->sortable(),

                TextColumn::make('gender')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('date_of_birth')
                    ->label('DOB')
                    ->date()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('state_of_origin')
                    ->label('State')
                    ->toggleable(),

                TextColumn::make('enrolments_count')
                    ->label('Enrolments')
                    ->counts('enrolments')
                    ->badge()
                    ->color(fn (int $state): string => $state > 1 ? 'success' : 'gray')
                    ->tooltip('2+ enrolments = returning graduate (NCE → Degree)'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                TrashedFilter::make(),
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