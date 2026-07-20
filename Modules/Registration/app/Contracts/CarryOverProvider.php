<?php

namespace Modules\Registration\Contracts;

/**
 * Supplies the course ids a student must carry over (re-register) because they
 * previously failed them. Carry-overs originate from results, which live in the
 * Assessments module. Until that exists, an interim provider returns none; bind
 * an Assessments-backed implementation later with no Registration changes.
 */
interface CarryOverProvider
{
    /** @return array<int, int> course ids to carry over for this enrolment */
    public function carryOverCourseIds(int $enrolmentId): array;
}
