<?php

namespace Modules\People\Filament\Resources\Enrolments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EnrolmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('person_id')
                    ->relationship('person', 'id')
                    ->required(),
                TextInput::make('matric_number')
                    ->required(),
                TextInput::make('programme_type')
                    ->required(),
                TextInput::make('entry_route')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('graduation_outcome'),
                DatePicker::make('graduated_at'),
                Select::make('admission_session_id')
                    ->relationship('admissionSession', 'name'),
                TextInput::make('programme_id')
                    ->numeric(),
                TextInput::make('current_level_id')
                    ->numeric(),
                TextInput::make('subject_combination_id')
                    ->numeric(),
            ]);
    }
}
