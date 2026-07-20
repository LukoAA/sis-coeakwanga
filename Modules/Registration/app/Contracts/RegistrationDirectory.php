<?php

namespace Modules\Registration\Contracts;

use Illuminate\Support\Collection;

/**
 * Public read surface other modules use to ask "is this student registered for
 * course X this session?" — e.g. Examinations gating exam dockets, Assessments
 * checking a student may be scored in a course.
 */
interface RegistrationDirectory
{
    public function isRegisteredFor(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool;

    /** @return Collection<int, int> registered course ids */
    public function registeredCourseIds(int $enrolmentId, int $sessionId, int $semester): Collection;
}
