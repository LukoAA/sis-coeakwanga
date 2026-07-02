<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\ApplicationResource;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;
}
