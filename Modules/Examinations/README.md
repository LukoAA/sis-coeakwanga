# Examinations

Exam eligibility and dockets — the pass a student presents to sit an exam. Where
Registration, Finance, and (later) Attendance converge into a single gate.

## Tables

| Table | Purpose |
|---|---|
| `exam_dockets` | One per enrolment + course + session + semester. Unique docket number, plus an eligibility snapshot (registered / fee-cleared / attendance) and `issued_at`. |

## The eligibility gate

A student may sit a course's exam only when ALL pass:

1. **Registered** for the course this session+semester — `RegistrationDirectory` (Registration)
2. **Fee-cleared for exams** (brief: 100%) — `FeeClearance::isClearedForExams` (Finance)
3. **Attendance met** (brief: 75%) — `AttendanceGate` (Examinations)

`ExamEligibilityService::evaluate()` returns each flag plus human-readable reasons;
`DocketService::issue()` grants a docket only if eligible, else throws
`NotEligibleForExamException` carrying the reasons.

## The attendance seam

Attendance data lives in the Attendance module, **not built yet**. `AttendanceGate`
has an `InterimAttendanceGate` that always passes. When Attendance lands, bind its
real implementation — the 75% rule activates with **no Examinations changes**:

```php
$this->app->bind(
    \Modules\Examinations\Contracts\AttendanceGate::class,
    \Modules\Attendance\Services\AttendanceGateService::class, // future
);
```

## Bindings (service provider `register()`)

```php
$this->app->bind(\Modules\Examinations\Contracts\AttendanceGate::class,
    \Modules\Examinations\Services\InterimAttendanceGate::class);
$this->app->bind(\Modules\Examinations\Contracts\ExaminationsDirectory::class,
    \Modules\Examinations\Services\ExaminationsDirectoryService::class);
```

`ExamEligibilityService` and `DocketService` autowire (their deps are all bound:
`RegistrationDirectory`, `FeeClearance`, `AttendanceGate`).

## Contract other modules use

```php
use Modules\Examinations\Contracts\ExaminationsDirectory;

$directory->hasValidDocket($enrolmentId, $courseId, $sessionId, $semester);
// e.g. Assessments confirming a student was cleared to sit before scoring
```

## Invariants (tested)

- Eligible (registered + fee-cleared + attendance) → docket issued with snapshot.
- Unregistered / fee-uncleared / attendance-failed → refused, with reasons.
- Issuance is idempotent; batch issue skips ineligible courses without throwing.

## Run

```bash
php artisan module:migrate Examinations
php artisan module:seed Examinations
php artisan test Modules/Examinations/tests
```
