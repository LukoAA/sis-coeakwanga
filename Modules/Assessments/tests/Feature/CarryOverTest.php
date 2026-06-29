<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\Programme;
use Modules\Assessments\Models\GradeBand;
use Modules\Assessments\Models\GradingScale;
use Modules\Assessments\Services\AssessmentsCarryOverProvider;
use Modules\Assessments\Services\GradingEngine;
use Modules\Assessments\Services\ResultWorkflow;
use Modules\Assessments\Services\ScoreEntryService;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

function scaleForCarryOver(): void
{
    $scale = GradingScale::create(['programme_type' => Programme::TYPE_NCE, 'name' => 'NCE']);
    foreach ([[40, 100, 'P', 4.00, true], [0, 39.99, 'F', 0.00, false]] as [$min, $max, $l, $p, $pass]) {
        GradeBand::create(['grading_scale_id' => $scale->id, 'min_score' => $min, 'max_score' => $max, 'grade_letter' => $l, 'grade_point' => $p, 'is_pass' => $pass]);
    }
}

it('reports a published failed course as a carry-over', function () {
    scaleForCarryOver();
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create();

    $passed = Course::factory()->create(['credit_units' => 2]);
    $failed = Course::factory()->create(['credit_units' => 3]);

    $scores = new ScoreEntryService(new GradingEngine());
    $flow = new ResultWorkflow();

    $pEntry = $scores->enterScore($enrolment, $passed, $session, 1, 30, 50); // 80 -> pass
    $fEntry = $scores->enterScore($enrolment, $failed, $session, 1, 10, 20); // 30 -> fail
    foreach ([$pEntry, $fEntry] as $e) {
        $flow->publish($flow->approve($flow->vet($flow->submit($e))));
    }

    $carryOvers = (new AssessmentsCarryOverProvider())->carryOverCourseIds($enrolment->id);

    expect($carryOvers)->toContain($failed->id)
        ->and($carryOvers)->not->toContain($passed->id);
});

it('does not report an unpublished fail as a carry-over', function () {
    scaleForCarryOver();
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create();
    $failed = Course::factory()->create(['credit_units' => 3]);

    // Failed but never published.
    (new ScoreEntryService(new GradingEngine()))->enterScore($enrolment, $failed, $session, 1, 10, 20);

    expect((new AssessmentsCarryOverProvider())->carryOverCourseIds($enrolment->id))->toBeEmpty();
});
