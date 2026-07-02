<?php

namespace Modules\People\Filament\Resources\People\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\People\Filament\Resources\People\PersonResource;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;
}
