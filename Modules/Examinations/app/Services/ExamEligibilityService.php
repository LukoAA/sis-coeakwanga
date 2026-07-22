<?php

namespace Modules\Examinations\Services;

use Modules\Examinations\Contracts\AttendanceGate;
use Modules\Finance\Contracts\FeeClearance;
use Modules\Registration\Contracts\RegistrationDirectory;

/**
 * Decides whether a student may sit a course's exam. Three gates, all read
 * through contracts so their real implementations swap freely:
 *   1. Registered for the course this session+semester   (RegistrationDirectory)
 *   2. Fee-cleared for exams (brief: 100%)                (FeeClearance)
 *   3. Attendance requirement met (brief: 75%)            (AttendanceGate — interim now)
 */
class ExamEligibilityService
{
    public function __construct(
        private readonly RegistrationDirectory $registrations,
        private readonly FeeClearance $clearance,
        private readonly AttendanceGate $attendance,
    ) {}

    /**
     * @return array{eligible: bool, registered: bool, fee_cleared: bool, attendance_ok: bool, reasons: array<int, string>}
     */
    public function evaluate(int $enrolmentId, int $courseId, int $sessionId, int $semester): array
    {
        $registered = $this->registrations->isRegisteredFor($enrolmentId, $courseId, $sessionId, $semester);
        $feeCleared = $this->clearance->isClearedForExams($enrolmentId, $sessionId);
        $attendanceOk = $this->attendance->meetsAttendance($enrolmentId, $courseId, $sessionId, $semester);

        $reasons = [];
        if (! $registered) {
            $reasons[] = 'Not registered for this course.';
        }
        if (! $feeCleared) {
            $reasons[] = 'Exam fee clearance not met.';
        }
        if (! $attendanceOk) {
            $reasons[] = 'Attendance requirement not met.';
        }

        return [
            'eligible' => $registered && $feeCleared && $attendanceOk,
            'registered' => $registered,
            'fee_cleared' => $feeCleared,
            'attendance_ok' => $attendanceOk,
            'reasons' => $reasons,
        ];
    }

    public function isEligible(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool
    {
        return $this->evaluate($enrolmentId, $courseId, $sessionId, $semester)['eligible'];
    }
}
