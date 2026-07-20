<?php

namespace App\Filament\Student\Resources\ScoreEntries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScoreEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('enrolment_id')
                    ->relationship('enrolment', 'id')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                Select::make('academic_session_id')
                    ->relationship('academicSession', 'name')
                    ->required(),
                TextInput::make('semester')
                    ->required()
                    ->numeric(),
                TextInput::make('credit_units')
                    ->required()
                    ->numeric(),
                TextInput::make('ca_score')
                    ->numeric(),
                TextInput::make('exam_score')
                    ->numeric(),
                TextInput::make('total')
                    ->numeric(),
                TextInput::make('grade'),
                TextInput::make('grade_point')
                    ->numeric(),
                Toggle::make('passed'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
            ]);
    }
}
