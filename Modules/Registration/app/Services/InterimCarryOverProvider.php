<?php

namespace Modules\Registration\Services;

use Modules\Registration\Contracts\CarryOverProvider;

/** No results module yet, so no carry-overs. Replace when Assessments lands. */
class InterimCarryOverProvider implements CarryOverProvider
{
    public function carryOverCourseIds(int $enrolmentId): array
    {
        return [];
    }
}
