<?php

namespace Modules\Academics\Filament\Resources\Programmes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Academics\Filament\Resources\Programmes\ProgrammeResource;

class ViewProgramme extends ViewRecord
{
    protected static string $resource = ProgrammeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
