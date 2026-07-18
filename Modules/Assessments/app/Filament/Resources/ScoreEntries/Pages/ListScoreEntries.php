<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Assessments\Filament\Resources\ScoreEntries\ScoreEntryResource;

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
