<?php

namespace Modules\Academics\Filament\Resources\Levels\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LevelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('programme_type'),
                TextEntry::make('code'),
                TextEntry::make('label'),
                TextEntry::make('rank')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
