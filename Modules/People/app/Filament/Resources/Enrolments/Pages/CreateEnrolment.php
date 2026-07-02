<?php

namespace Modules\People\Filament\Resources\Enrolments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\People\Filament\Resources\Enrolments\EnrolmentResource;

class CreateEnrolment extends CreateRecord
{
    protected static string $resource = EnrolmentResource::class;
}
