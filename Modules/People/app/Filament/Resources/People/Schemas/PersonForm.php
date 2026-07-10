<?php

namespace Modules\People\Filament\Resources\People\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Modules\People\Data\NigeriaStates;

class PersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->components([
                        Section::make('Passport')
                            ->columnStart(2)
                            ->columnSpan(1)
                            ->components([
                                SpatieMediaLibraryFileUpload::make('photo')
                                    ->collection('photo')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->conversion('id_card')
                                    ->label('')
                                    ->alignCenter(),
                            ]),
                    ]),

                Section::make('Identity')
                    ->description('Required core — enough to identify and match a person.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsible()
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
                            ->maxDate(now())
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact')
                    ->columnSpanFull()
                    ->columns(3)
                    ->collapsible()
                    ->components([
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email(),
                        Select::make('state_of_origin')
                            ->label('State of origin')
                            ->options(NigeriaStates::stateNames())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('lga', null)),

                        Select::make('lga')
                            ->label('LGA')
                            ->options(fn (Get $get): array => NigeriaStates::lgasFor($get('state_of_origin')))
                            ->searchable(),
                                        ]),

                Section::make('Next of kin')
                    ->columnSpanFull()
                    ->columns(3)
                    ->collapsible()
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