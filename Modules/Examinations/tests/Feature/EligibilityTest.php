<?php

use Illuminate\Support\Collection;
use Modules\Examinations\Contracts\AttendanceGate;
use Modules\Examinations\Services\ExamEligibilityService;
use Modules\Examinations\Services\InterimAttendanceGate;
use Modules\Finance\Contracts\FeeClearance;
use Modules\Registration\Contracts\RegistrationDirectory;

/**
 * Test doubles let us drive each gate independently without standing up the full
 * Registration + Finance data graph — the point here is the eligibility LOGIC.
 */
function fakeRegistrations(bool $registered): RegistrationDirectory
{
    return new class($registered) implements RegistrationDirectory {
        public function __construct(private bool $r) {}
        public function isRegisteredFor(int $e, int $c, int $s, int $sem): bool { return $this->r; }
        public function registeredCourseIds(int $e, int $s, int $sem): Collection { return collect(); }
    };
}

function fakeClearance(bool $exams): FeeClearance
{
    return new class($exams) implements FeeClearance {
        public function __construct(private bool $x) {}
        public function percentPaid(int $enrolmentId, int $sessionId): float { return $this->x ? 100.0 : 0.0; }
        public function isClearedToRegister(int $enrolmentId, int $sessionId): bool { return $this->x; }
        public function isClearedForExams(int $enrolmentId, int $sessionId): bool { return $this->x; }
    };
}

function eligibility(bool $registered, bool $feeCleared, ?AttendanceGate $attendance = null): ExamEligibilityService
{
    return new ExamEligibilityService(
        fakeRegistrations($registered),
        fakeClearance($feeCleared),
        $attendance ?? new InterimAttendanceGate(),
    );
}

it('is eligible when registered and fee-cleared (attendance interim-passes)', function () {
    $result = eligibility(true, true)->evaluate(1, 1, 1, 1);

    expect($result['eligible'])->toBeTrue()
        ->and($result['registered'])->toBeTrue()
        ->and($result['fee_cleared'])->toBeTrue()
        ->and($result['attendance_ok'])->toBeTrue()
        ->and($result['reasons'])->toBeEmpty();
});

it('blocks an unregistered student with a clear reason', function () {
    $result = eligibility(false, true)->evaluate(1, 1, 1, 1);

    expect($result['eligible'])->toBeFalse()
        ->and($result['reasons'])->toContain('Not registered for this course.');
});

it('blocks a fee-uncleared student', function () {
    $result = eligibility(true, false)->evaluate(1, 1, 1, 1);

    expect($result['eligible'])->toBeFalse()
        ->and($result['reasons'])->toContain('Exam fee clearance not met.');
});

it('blocks when the attendance gate fails (proving the seam works)', function () {
    $failAttendance = new class implements AttendanceGate {
        public function meetsAttendance(int $e, int $c, int $s, int $sem): bool { return false; }
    };

    $result = eligibility(true, true, $failAttendance)->evaluate(1, 1, 1, 1);

    expect($result['eligible'])->toBeFalse()
        ->and($result['reasons'])->toContain('Attendance requirement not met.');
});
