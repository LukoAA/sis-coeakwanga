<?php

namespace Modules\Academics\Filament\Resources\Schools\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Academics\Filament\Resources\Schools\SchoolResource;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;
}
