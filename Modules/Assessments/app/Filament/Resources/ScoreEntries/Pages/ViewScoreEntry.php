<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Assessments\Filament\Resources\ScoreEntries\ScoreEntryResource;

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
