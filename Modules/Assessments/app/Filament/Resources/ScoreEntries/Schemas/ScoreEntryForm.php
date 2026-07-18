<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Identity\Contracts\AcademicContext;
use Modules\Identity\Models\Setting;

class ScoreEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        $caMax = (float) Setting::get('assessments.ca_max', 30);
        $examMax = (float) Setting::get('assessments.exam_max', 70);

        $context = app(AcademicContext::class);
        $session = $context->currentSession();
        $semester = $context->currentSemester();

        return $schema
            ->components([
                Placeholder::make('period')
                    ->label('Entering results for')
                    ->content(($session?->name ?? 'No current session!') . ' — ' . ($semester?->name ?? 'No current semester!') . ' Semester'),

                Select::make('enrolment_id')
                    ->relationship('enrolment', 'matric_number')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Student (matric number)'),

                Select::make('course_id')
                    ->relationship('course', 'code')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Course'),

                TextInput::make('ca_score')
                    ->label("CA score (max {$caMax})")
                    ->numeric()->minValue(0)->maxValue($caMax)->required(),

                TextInput::make('exam_score')
                    ->label("Exam score (max {$examMax})")
                    ->numeric()->minValue(0)->maxValue($examMax)->required(),
            ]);
    }
}