<?php

namespace Modules\Academics\Filament\Resources\Levels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('programme_type')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('rank')
                    ->required()
                    ->numeric(),
            ]);
    }
}
