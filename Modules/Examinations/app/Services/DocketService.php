<?php

namespace Modules\Examinations\Services;

use Illuminate\Support\Facades\DB;
use Modules\Academics\Models\Course;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Examinations\Exceptions\NotEligibleForExamException;
use Modules\Examinations\Models\ExamDocket;

/**
 * Issues exam dockets. A docket is granted only when the eligibility gate passes;
 * it records a snapshot of why (registered / fee-cleared / attendance) and a
 * unique docket number the student presents to sit the exam.
 */
class DocketService
{
    public function __construct(private readonly ExamEligibilityService $eligibility) {}

    /**
     * Issue (or return the existing) docket for a student in a course.
     *
     * @throws NotEligibleForExamException when the student fails any gate
     */
    public function issue(Enrolment $enrolment, Course $course, AcademicSession $session, int $semester): ExamDocket
    {
        $result = $this->eligibility->evaluate($enrolment->id, $course->id, $session->id, $semester);

        if (! $result['eligible']) {
            throw new NotEligibleForExamException($result['reasons']);
        }

        return DB::transaction(function () use ($enrolment, $course, $session, $semester, $result) {
            $existing = ExamDocket::query()
                ->where('enrolment_id', $enrolment->id)
                ->where('course_id', $course->id)
                ->where('academic_session_id', $session->id)
                ->where('semester', $semester)
                ->first();

            if ($existing) {
                return $existing;
            }

            return ExamDocket::create([
                'docket_number' => $this->generateDocketNumber($enrolment, $session),
                'enrolment_id' => $enrolment->id,
                'course_id' => $course->id,
                'academic_session_id' => $session->id,
                'semester' => $semester,
                'registered' => $result['registered'],
                'fee_cleared' => $result['fee_cleared'],
                'attendance_ok' => $result['attendance_ok'],
                'issued_at' => now(),
            ]);
        });
    }

    /**
     * Issue dockets for every course the student is eligible in this
     * session+semester. Ineligible courses are skipped (not thrown on).
     *
     * @return array{issued: array<int, ExamDocket>, skipped: array<int, array{course_id: int, reasons: array<int, string>}>}
     */
    public function issueAllEligible(Enrolment $enrolment, AcademicSession $session, int $semester, array $courseIds): array
    {
        $issued = [];
        $skipped = [];

        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);
            if (! $course) {
                continue;
            }

            $result = $this->eligibility->evaluate($enrolment->id, $courseId, $session->id, $semester);
            if ($result['eligible']) {
                $issued[] = $this->issue($enrolment, $course, $session, $semester);
            } else {
                $skipped[] = ['course_id' => $courseId, 'reasons' => $result['reasons']];
            }
        }

        return ['issued' => $issued, 'skipped' => $skipped];
    }

    private function generateDocketNumber(Enrolment $enrolment, AcademicSession $session): string
    {
        // e.g. DKT/2025/000123/8  — session year / enrolment / short random
        $year = preg_replace('/\D/', '', (string) $session->name) ?: date('Y');
        $year = substr($year, 0, 4);

        return sprintf('DKT/%s/%06d/%s', $year, $enrolment->id, strtoupper(substr(md5(uniqid('', true)), 0, 4)));
    }
}
