<?php

namespace Modules\Examinations\Contracts;

/**
 * Public read surface other modules use to ask "does this student hold a valid
 * exam docket for course X?" — e.g. Assessments confirming a student was cleared
 * to sit before a score is entered.
 */
interface ExaminationsDirectory
{
    public function hasValidDocket(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool;
}
