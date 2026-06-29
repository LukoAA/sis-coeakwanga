<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\DB;
use Modules\Assessments\Models\ResultSummary;
use Modules\Assessments\Models\ScoreEntry;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

/**
 * Computes GPA (for one session+semester) and cumulative CGPA (across all
 * published results) from published score entries, weighted by credit units,
 * and records a result summary with the resulting classification.
 *
 * GPA = sum(grade_point * credit_units) / sum(credit_units)
 */
class GpaCalculator
{
    public function __construct(private readonly ClassificationService $classifier) {}

    public function computeSemester(Enrolment $enrolment, AcademicSession $session, int $semester): ResultSummary
    {
        return DB::transaction(function () use ($enrolment, $session, $semester) {
            $semesterEntries = ScoreEntry::query()->published()
                ->where('enrolment_id', $enrolment->id)
                ->where('academic_session_id', $session->id)
                ->where('semester', $semester)
                ->get();

            [$tcu, $tgp, $gpa] = $this->weighted($semesterEntries);

            // CGPA over ALL published entries for the enrolment (cumulative).
            $allEntries = ScoreEntry::query()->published()
                ->where('enrolment_id', $enrolment->id)
                ->get();

            [, , $cgpa] = $this->weighted($allEntries);

            $classification = $this->classifier->classify($enrolment->programme_type, $cgpa);

            return ResultSummary::updateOrCreate(
                [
                    'enrolment_id' => $enrolment->id,
                    'academic_session_id' => $session->id,
                    'semester' => $semester,
                ],
                [
                    'tcu' => $tcu,
                    'tgp' => $tgp,
                    'gpa' => $gpa,
                    'cgpa' => $cgpa,
                    'classification' => $classification,
                ],
            );
        });
    }

    /** @return array{0:int,1:float,2:float} [tcu, tgp, gpa] */
    private function weighted($entries): array
    {
        $tcu = 0;
        $tgp = 0.0;

        foreach ($entries as $entry) {
            $units = (int) $entry->credit_units;
            $tcu += $units;
            $tgp += (float) $entry->grade_point * $units;
        }

        $gpa = $tcu > 0 ? round($tgp / $tcu, 2) : 0.0;

        return [$tcu, round($tgp, 2), $gpa];
    }
}
