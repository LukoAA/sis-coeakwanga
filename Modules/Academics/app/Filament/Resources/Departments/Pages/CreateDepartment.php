<?php

namespace Modules\Academics\Filament\Resources\Departments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Academics\Filament\Resources\Departments\DepartmentResource;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}
