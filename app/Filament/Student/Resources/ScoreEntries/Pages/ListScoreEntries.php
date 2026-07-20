<?php

namespace App\Filament\Student\Resources\ScoreEntries\Pages;

use App\Filament\Student\Resources\ScoreEntries\ScoreEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScoreEntries extends ListRecords
{
    protected static string $resource = ScoreEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
