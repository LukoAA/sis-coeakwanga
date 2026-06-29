<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\DB;
use Modules\Academics\Models\Course;
use Modules\Assessments\Models\ScoreEntry;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class ScoreEntryService
{
    public function __construct(private readonly GradingEngine $engine) {}

    /**
     * Record a CA + exam score for a student in a course. Computes the total,
     * looks up the grade/point/pass from the configurable bands, and stores a
     * draft entry ready to enter the approval workflow.
     */
    public function enterScore(
        Enrolment $enrolment,
        Course $course,
        AcademicSession $session,
        int $semester,
        float $ca,
        float $exam,
    ): ScoreEntry {
        return DB::transaction(function () use ($enrolment, $course, $session, $semester, $ca, $exam) {
            $total = round($ca + $exam, 2);
            $band = $this->engine->gradeFor($enrolment->programme_type, $total);

            return ScoreEntry::updateOrCreate(
                [
                    'enrolment_id' => $enrolment->id,
                    'course_id' => $course->id,
                    'academic_session_id' => $session->id,
                    'semester' => $semester,
                ],
                [
                    'credit_units' => $course->credit_units,
                    'ca_score' => $ca,
                    'exam_score' => $exam,
                    'total' => $total,
                    'grade' => $band?->grade_letter,
                    'grade_point' => $band?->grade_point,
                    'passed' => $band?->is_pass,
                    'status' => ScoreEntry::STATUS_DRAFT,
                ],
            );
        });
    }
}
