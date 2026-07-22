<?php

use Illuminate\Support\Collection;
use Modules\Academics\Models\Course;
use Modules\Examinations\Contracts\AttendanceGate;
use Modules\Examinations\Exceptions\NotEligibleForExamException;
use Modules\Examinations\Models\ExamDocket;
use Modules\Examinations\Services\DocketService;
use Modules\Examinations\Services\ExamEligibilityService;
use Modules\Examinations\Services\InterimAttendanceGate;
use Modules\Finance\Contracts\FeeClearance;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Registration\Contracts\RegistrationDirectory;

function regs(bool $registered): RegistrationDirectory
{
    return new class($registered) implements RegistrationDirectory {
        public function __construct(private bool $r) {}
        public function isRegisteredFor(int $e, int $c, int $s, int $sem): bool { return $this->r; }
        public function registeredCourseIds(int $e, int $s, int $sem): Collection { return collect(); }
    };
}

function clear(bool $ok): FeeClearance
{
    return new class($ok) implements FeeClearance {
        public function __construct(private bool $x) {}
        public function percentPaid(int $e, int $s): float { return $this->x ? 100.0 : 0.0; }
        public function isClearedToRegister(int $e, int $s): bool { return $this->x; }
        public function isClearedForExams(int $e, int $s): bool { return $this->x; }
    };
}

function docketService(bool $registered, bool $feeCleared, ?AttendanceGate $att = null): DocketService
{
    return new DocketService(new ExamEligibilityService(
        regs($registered), clear($feeCleared), $att ?? new InterimAttendanceGate(),
    ));
}

it('issues a docket to an eligible student with an eligibility snapshot', function () {
    $enrolment = Enrolment::factory()->nce()->create();
    $course = Course::factory()->create();
    $session = AcademicSession::factory()->create();

    $docket = docketService(true, true)->issue($enrolment, $course, $session, 1);

    expect($docket)->toBeInstanceOf(ExamDocket::class)
        ->and($docket->docket_number)->toStartWith('DKT/')
        ->and($docket->registered)->toBeTrue()
        ->and($docket->fee_cleared)->toBeTrue()
        ->and($docket->issued_at)->not->toBeNull();
});

it('refuses a docket to an ineligible student and explains why', function () {
    $enrolment = Enrolment::factory()->nce()->create();
    $course = Course::factory()->create();
    $session = AcademicSession::factory()->create();

    expect(fn () => docketService(false, true)->issue($enrolment, $course, $session, 1))
        ->toThrow(NotEligibleForExamException::class);
});

it('is idempotent — issuing twice returns the same docket', function () {
    $enrolment = Enrolment::factory()->nce()->create();
    $course = Course::factory()->create();
    $session = AcademicSession::factory()->create();

    $svc = docketService(true, true);
    $first = $svc->issue($enrolment, $course, $session, 1);
    $second = $svc->issue($enrolment, $course, $session, 1);

    expect($second->id)->toBe($first->id)
        ->and(ExamDocket::count())->toBe(1);
});

it('issues dockets for eligible courses and skips ineligible ones', function () {
    $enrolment = Enrolment::factory()->nce()->create();
    $session = AcademicSession::factory()->create();
    $c1 = Course::factory()->create();
    $c2 = Course::factory()->create();

    // Eligible for everything here (both registered), so both issue.
    $out = docketService(true, true)->issueAllEligible($enrolment, $session, 1, [$c1->id, $c2->id]);

    expect($out['issued'])->toHaveCount(2)
        ->and($out['skipped'])->toBeEmpty();
});
