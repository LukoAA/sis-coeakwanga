<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\Widget;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

class StudentProfileHeader extends Widget
{
    protected string $view = 'filament.student.widgets.student-profile-header';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1; // always first, above the stats

    public function getViewData(): array
    {
        $person = Person::find(auth()->user()?->person_id);

        $enrolment = $person
            ? Enrolment::with(['programme', 'level'])
                ->where('person_id', $person->id)
                ->where('status', Enrolment::STATUS_ACTIVE)
                ->latest()
                ->first()
            : null;

        return [
            'person' => $person,
            'enrolment' => $enrolment,
            'photoUrl' => $person?->getFirstMediaUrl('photo', 'id_card') ?: null,
        ];
    }
}