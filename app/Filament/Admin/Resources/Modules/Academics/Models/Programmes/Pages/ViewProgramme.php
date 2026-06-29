<?php

namespace App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages;

use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\ProgrammeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

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
