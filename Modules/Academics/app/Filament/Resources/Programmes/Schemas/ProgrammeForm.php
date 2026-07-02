<?php

namespace Modules\Academics\Filament\Resources\Programmes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgrammeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('programme_type')
                    ->required(),
                TextInput::make('award'),
                TextInput::make('duration_years')
                    ->required()
                    ->numeric()
                    ->default(3),
            ]);
    }
}
