<?php

namespace Modules\Identity\Contracts;

use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Semester;

/**
 * The public contract for reading and setting the institution's current
 * academic period. Every other module depends on THIS interface (resolved
 * from the container) rather than reaching into Identity's models directly.
 */
interface AcademicContext
{
    public function currentSession(): ?AcademicSession;

    public function currentSemester(): ?Semester;

    /** Make $session the current one, clearing any previous current session. */
    public function setCurrentSession(AcademicSession $session): void;

    /** Make $semester the current one, clearing any previous current semester. */
    public function setCurrentSemester(Semester $semester): void;
}
