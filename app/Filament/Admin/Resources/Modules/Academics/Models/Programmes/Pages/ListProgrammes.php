<?php

namespace App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages;

use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\ProgrammeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgrammes extends ListRecords
{
    protected static string $resource = ProgrammeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
