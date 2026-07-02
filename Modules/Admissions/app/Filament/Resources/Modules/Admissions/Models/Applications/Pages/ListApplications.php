<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\ApplicationResource;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
