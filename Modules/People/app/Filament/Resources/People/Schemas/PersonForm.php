<?php

namespace Modules\People\Filament\Resources\People\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->description('Required core — enough to identify and match a person.')
                    ->columns(2)
                    ->components([
                        TextInput::make('surname')
                            ->required(),
                        TextInput::make('first_name')
                            ->required(),
                        TextInput::make('other_names'),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->required()
                            ->maxDate(now()),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->components([
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email(),
                        TextInput::make('state_of_origin'),
                        TextInput::make('lga')
                            ->label('LGA'),
                    ]),

                Section::make('Next of kin')
                    ->columns(2)
                    ->collapsed()
                    ->components([
                        TextInput::make('next_of_kin_name'),
                        TextInput::make('next_of_kin_phone')
                            ->tel(),
                        TextInput::make('next_of_kin_relationship'),
                    ]),
            ]);
    }
}