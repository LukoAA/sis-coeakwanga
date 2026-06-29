<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\Programme;
use Modules\Assessments\Models\Classification;
use Modules\Assessments\Models\GradeBand;
use Modules\Assessments\Models\GradingScale;
use Modules\Assessments\Models\ScoreEntry;
use Modules\Assessments\Services\ClassificationService;
use Modules\Assessments\Services\GpaCalculator;
use Modules\Assessments\Services\GradingEngine;
use Modules\Assessments\Services\ResultWorkflow;
use Modules\Assessments\Services\ScoreEntryService;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

function fullSetup(): void
{
    $scale = GradingScale::create(['programme_type' => Programme::TYPE_NCE, 'name' => 'NCE']);
    foreach ([[70, 100, 'A', 5.00, true], [60, 69.99, 'B', 4.00, true], [50, 59.99, 'C', 3.00, true], [40, 49.99, 'D', 1.00, true], [0, 39.99, 'F', 0.00, false]] as [$min, $max, $l, $p, $pass]) {
        GradeBand::create(['grading_scale_id' => $scale->id, 'min_score' => $min, 'max_score' => $max, 'grade_letter' => $l, 'grade_point' => $p, 'is_pass' => $pass]);
    }
    foreach ([[4.50, 5.00, 'Distinction'], [3.50, 4.49, 'Upper Credit'], [2.40, 3.49, 'Lower Credit'], [1.50, 2.39, 'Merit'], [1.00, 1.49, 'Pass']] as [$min, $max, $label]) {
        Classification::create(['programme_type' => Programme::TYPE_NCE, 'min_cgpa' => $min, 'max_cgpa' => $max, 'label' => $label]);
    }
}

it('computes a credit-unit-weighted GPA from published results', function () {
    fullSetup();
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create();

    $c3 = Course::factory()->create(['credit_units' => 3]); // score 80 -> A (5.0)
    $c2 = Course::factory()->create(['credit_units' => 2]); // score 65 -> B (4.0)

    $scores = new ScoreEntryService(new GradingEngine());
    $flow = new ResultWorkflow();

    foreach ([[$c3, 30, 50], [$c2, 25, 40]] as [$course, $ca, $exam]) {
        $entry = $scores->enterScore($enrolment, $course, $session, 1, $ca, $exam);
        $flow->publish($flow->approve($flow->vet($flow->submit($entry))));
    }

    $summary = (new GpaCalculator(new ClassificationService()))->computeSemester($enrolment, $session, 1);

    // GPA = (5.0*3 + 4.0*2) / (3+2) = 23/5 = 4.60
    expect($summary->tcu)->toBe(5)
        ->and((float) $summary->gpa)->toBe(4.60)
        ->and((float) $summary->cgpa)->toBe(4.60)
        ->and($summary->classification)->toBe('Distinction');
});

it('excludes unpublished results from the GPA', function () {
    fullSetup();
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create();
    $course = Course::factory()->create(['credit_units' => 3]);

    // Entered and graded, but NOT advanced to published.
    (new ScoreEntryService(new GradingEngine()))->enterScore($enrolment, $course, $session, 1, 30, 50);

    $summary = (new GpaCalculator(new ClassificationService()))->computeSemester($enrolment, $session, 1);

    expect($summary->tcu)->toBe(0)
        ->and((float) $summary->gpa)->toBe(0.0);
});
