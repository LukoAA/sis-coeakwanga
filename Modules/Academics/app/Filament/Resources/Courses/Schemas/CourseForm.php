<?php

namespace Modules\Academics\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required(),
                TextInput::make('programme_type')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('credit_units')
                    ->required()
                    ->numeric()
                    ->default(2),
                TextInput::make('course_type')
                    ->required(),
            ]);
    }
}
