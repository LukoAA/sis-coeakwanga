<?php

namespace Modules\Examinations\Services;

use Modules\Examinations\Contracts\AttendanceGate;

/**
 * No Attendance module yet, so attendance cannot be verified — pass everyone.
 * Replace with an Attendance-backed gate once that module lands; the 75% rule
 * then activates with no changes here.
 */
class InterimAttendanceGate implements AttendanceGate
{
    public function meetsAttendance(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool
    {
        return true;
    }
}
