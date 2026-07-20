<?php

namespace App\Filament\Student\Resources\ScoreEntries\Pages;

use App\Filament\Student\Resources\ScoreEntries\ScoreEntryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScoreEntry extends ViewRecord
{
    protected static string $resource = ScoreEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
