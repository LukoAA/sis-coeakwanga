<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Assessments\Filament\Resources\ScoreEntries\ScoreEntryResource;

class EditScoreEntry extends EditRecord
{
    protected static string $resource = ScoreEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
