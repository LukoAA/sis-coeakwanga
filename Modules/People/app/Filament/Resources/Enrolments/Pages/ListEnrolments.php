<?php

namespace Modules\People\Filament\Resources\Enrolments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\People\Filament\Resources\Enrolments\EnrolmentResource;

class ListEnrolments extends ListRecords
{
    protected static string $resource = EnrolmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
