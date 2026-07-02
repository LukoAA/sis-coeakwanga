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

class EnrolmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('person.id')
                    ->searchable(),
                TextColumn::make('matric_number')
                    ->searchable(),
                TextColumn::make('programme_type')
                    ->searchable(),
                TextColumn::make('entry_route')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('graduation_outcome')
                    ->searchable(),
                TextColumn::make('graduated_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('admissionSession.name')
                    ->searchable(),
                TextColumn::make('programme_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_level_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject_combination_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
