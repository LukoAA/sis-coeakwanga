<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\SubjectCombination;
use Modules\Admissions\Models\Application;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant')
                    ->columns(2)
                    ->components([
                        TextInput::make('applicant_surname')->required(),
                        TextInput::make('applicant_first_name')->required(),
                        TextInput::make('applicant_other_names'),
                        Select::make('applicant_gender')
                            ->options(['male' => 'Male', 'female' => 'Female']),
                        DatePicker::make('applicant_dob')
                            ->label('Date of birth')
                            ->maxDate(now()),
                        TextInput::make('applicant_phone')->tel(),
                        TextInput::make('applicant_email')->email(),
                    ]),

                Section::make('Application')
                    ->columns(2)
                    ->components([
                        Select::make('academic_session_id')
                            ->relationship('academicSession', 'name')
                            ->required()
                            ->label('Session'),
                        Select::make('entry_route')
                            ->options([
                                Enrolment::ROUTE_UTME => 'UTME',
                                Enrolment::ROUTE_DIRECT_ENTRY => 'Direct Entry',
                            ])
                            ->required()
                            ->live(),
                        Select::make('programme_id')
                            ->relationship('programme', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Programme'),
                        Select::make('subject_combination_id')
                            ->relationship('subjectCombination', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Subject combination'),
                        TextInput::make('jamb_reg_no')->label('JAMB reg. no.'),
                        TextInput::make('applicant_nce_matric')
                            ->label('Prior NCE matric (for Direct Entry)')
                            ->visible(fn ($get) => $get('entry_route') === Enrolment::ROUTE_DIRECT_ENTRY),
                    ]),
            ]);
    }
}