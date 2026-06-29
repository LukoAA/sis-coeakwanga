<?php

namespace Modules\Assessments\Services;

use Modules\Assessments\Models\ScoreEntry;
use Modules\Registration\Contracts\CarryOverProvider;

/**
 * The REAL carry-over provider. A course is a carry-over when the student has a
 * PUBLISHED failing result for it. Bind this in place of Registration's
 * InterimCarryOverProvider and course forms auto-include failed courses — with
 * no Registration code changes.
 */
class AssessmentsCarryOverProvider implements CarryOverProvider
{
    public function carryOverCourseIds(int $enrolmentId): array
    {
        return ScoreEntry::query()->published()
            ->where('enrolment_id', $enrolmentId)
            ->where('passed', false)
            ->pluck('course_id')
            ->unique()
            ->values()
            ->all();
    }
}
