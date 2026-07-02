<?php

namespace Modules\People\Filament\Resources\Enrolments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Modules\People\Filament\Resources\Enrolments\EnrolmentResource;

class EditEnrolment extends EditRecord
{
    protected static string $resource = EnrolmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
