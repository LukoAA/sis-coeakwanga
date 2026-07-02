<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\ApplicationResource;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
