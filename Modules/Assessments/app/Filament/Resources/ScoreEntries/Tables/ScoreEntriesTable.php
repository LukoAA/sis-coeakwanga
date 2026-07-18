<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Assessments\Models\ScoreEntry;
use Modules\Assessments\Services\ResultWorkflow;

class ScoreEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('enrolment.matric_number')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.code')
                    ->label('Course')
                    ->searchable(),
                TextColumn::make('ca_score')->label('CA'),
                TextColumn::make('exam_score')->label('Exam'),
                TextColumn::make('total')->sortable(),
                TextColumn::make('grade')
                    ->badge()
                    ->color(fn (ScoreEntry $record) => $record->passed ? 'success' : 'danger'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ScoreEntry::STATUS_DRAFT => 'gray',
                        ScoreEntry::STATUS_SUBMITTED => 'info',
                        ScoreEntry::STATUS_VETTED => 'warning',
                        ScoreEntry::STATUS_APPROVED => 'primary',
                        ScoreEntry::STATUS_PUBLISHED => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('academicSession.name')->label('Session')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        ScoreEntry::STATUS_DRAFT => 'Draft',
                        ScoreEntry::STATUS_SUBMITTED => 'Submitted',
                        ScoreEntry::STATUS_VETTED => 'Vetted',
                        ScoreEntry::STATUS_APPROVED => 'Approved',
                        ScoreEntry::STATUS_PUBLISHED => 'Published',
                    ]),
                SelectFilter::make('course_id')
                    ->relationship('course', 'code')
                    ->label('Course'),
            ])
            ->recordActions([
                Action::make('submit')
                    ->icon('heroicon-o-paper-airplane')->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (ScoreEntry $r) => $r->status === ScoreEntry::STATUS_DRAFT)
                    ->action(function (ScoreEntry $r) {
                        app(ResultWorkflow::class)->submit($r, auth()->id());
                        Notification::make()->title('Result submitted')->success()->send();
                    }),
                Action::make('vet')
                    ->icon('heroicon-o-magnifying-glass')->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (ScoreEntry $r) => $r->status === ScoreEntry::STATUS_SUBMITTED)
                    ->action(function (ScoreEntry $r) {
                        app(ResultWorkflow::class)->vet($r, auth()->id());
                        Notification::make()->title('Result vetted')->success()->send();
                    }),
                Action::make('approve')
                    ->icon('heroicon-o-check-badge')->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (ScoreEntry $r) => $r->status === ScoreEntry::STATUS_VETTED)
                    ->action(function (ScoreEntry $r) {
                        app(ResultWorkflow::class)->approve($r, auth()->id());
                        Notification::make()->title('Result approved')->success()->send();
                    }),
                Action::make('publish')
                    ->icon('heroicon-o-globe-alt')->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Publish this result? Published results count toward GPA and carry-overs.')
                    ->visible(fn (ScoreEntry $r) => $r->status === ScoreEntry::STATUS_APPROVED)
                    ->action(function (ScoreEntry $r) {
                        app(ResultWorkflow::class)->publish($r, auth()->id());
                        Notification::make()->title('Result published')->success()->send();
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
}