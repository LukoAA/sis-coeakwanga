<?php

namespace Modules\Academics\Filament\Resources\Programmes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProgrammeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('department.name')
                    ->label('Department'),
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('programme_type'),
                TextEntry::make('award')
                    ->placeholder('-'),
                TextEntry::make('duration_years')
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
