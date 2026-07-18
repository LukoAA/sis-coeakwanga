<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScoreEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('enrolment.id')
                    ->label('Enrolment'),
                TextEntry::make('course.title')
                    ->label('Course'),
                TextEntry::make('academic_session_id')
                    ->numeric(),
                TextEntry::make('semester')
                    ->numeric(),
                TextEntry::make('credit_units')
                    ->numeric(),
                TextEntry::make('ca_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('exam_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('grade')
                    ->placeholder('-'),
                TextEntry::make('grade_point')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('passed')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
