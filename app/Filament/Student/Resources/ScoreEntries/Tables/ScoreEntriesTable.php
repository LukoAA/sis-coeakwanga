<?php

namespace App\Filament\Student\Resources\ScoreEntries\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Assessments\Models\ScoreEntry;

class ScoreEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicSession.name')->label('Session'),
                TextColumn::make('semester')
                    ->formatStateUsing(fn (int $state) => $state === 1 ? 'First' : 'Second'),
                TextColumn::make('course.code')->label('Course'),
                TextColumn::make('course.title')->label('Title')->toggleable(),
                TextColumn::make('credit_units')->label('Units'),
                TextColumn::make('total')->label('Score'),
                TextColumn::make('grade')
                    ->badge()
                    ->color(fn (ScoreEntry $record) => $record->passed ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('academic_session_id')
                    ->relationship('academicSession', 'name')
                    ->label('Session'),
            ])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([]);
    }
}