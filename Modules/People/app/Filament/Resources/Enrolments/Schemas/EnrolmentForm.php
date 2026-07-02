<?php

namespace Modules\People\Filament\Resources\Enrolments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\People\Models\Enrolment;

class EnrolmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('person_id')
                    ->relationship('person', 'surname')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Student (Person)'),

                TextInput::make('matric_number')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('programme_type')
                    ->options([
                        Enrolment::TYPE_NCE => 'NCE',
                        Enrolment::TYPE_DEGREE => 'Degree',
                    ])
                    ->required()
                    ->live(),

                Select::make('entry_route')
                    ->options([
                        Enrolment::ROUTE_UTME => 'UTME',
                        Enrolment::ROUTE_DIRECT_ENTRY => 'Direct Entry',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        Enrolment::STATUS_ACTIVE => 'Active',
                        Enrolment::STATUS_GRADUATED => 'Graduated',
                        Enrolment::STATUS_WITHDRAWN => 'Withdrawn',
                        Enrolment::STATUS_DEFERRED => 'Deferred',
                    ])
                    ->required()
                    ->default(Enrolment::STATUS_ACTIVE)
                    ->live(),

                Select::make('programme_id')
                    ->relationship('programme', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Programme'),

                Select::make('current_level_id')
                    ->relationship('level', 'label')
                    ->preload()
                    ->label('Level'),

                Select::make('subject_combination_id')
                    ->relationship('subjectCombination', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Subject combination'),

                Select::make('admission_session_id')
                    ->relationship('admissionSession', 'name')
                    ->preload()
                    ->label('Admission session'),

                TextInput::make('graduation_outcome')
                    ->visible(fn ($get) => $get('status') === Enrolment::STATUS_GRADUATED),

                DatePicker::make('graduated_at')
                    ->visible(fn ($get) => $get('status') === Enrolment::STATUS_GRADUATED),
            ]);
    }
}