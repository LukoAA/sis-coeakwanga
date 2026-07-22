<?php

namespace Modules\Examinations\Contracts;

/**
 * Whether a student has met the attendance requirement (brief: 75%) to sit a
 * course's exam. Attendance data lives in the Attendance module, which is not
 * built yet. Until then, an interim gate always passes; bind an Attendance-backed
 * implementation later with no Examinations changes.
 */
interface AttendanceGate
{
    public function meetsAttendance(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool;
}
