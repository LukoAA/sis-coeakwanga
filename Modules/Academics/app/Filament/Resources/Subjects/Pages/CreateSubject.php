<?php

namespace Modules\Academics\Filament\Resources\Subjects\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Academics\Filament\Resources\Subjects\SubjectResource;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;
}
