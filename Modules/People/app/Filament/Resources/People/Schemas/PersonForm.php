<?php

namespace Modules\People\Filament\Resources\People\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('surname')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('gender')
                    ->required(),
                DatePicker::make('date_of_birth')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('other_names'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('state_of_origin'),
                TextInput::make('lga'),
                TextInput::make('next_of_kin_name'),
                TextInput::make('next_of_kin_phone')
                    ->tel(),
                TextInput::make('next_of_kin_relationship'),
            ]);
    }
}
