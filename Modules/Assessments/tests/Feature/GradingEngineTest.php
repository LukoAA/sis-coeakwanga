<?php

use Modules\Academics\Models\Programme;
use Modules\Assessments\Models\GradeBand;
use Modules\Assessments\Models\GradingScale;
use Modules\Assessments\Services\GradingEngine;

function seedScale(string $type = 'NCE'): void
{
    $scale = GradingScale::create(['programme_type' => $type, 'name' => "$type scale"]);
    $bands = [
        [70, 100, 'A', 5.00, true],
        [60, 69.99, 'B', 4.00, true],
        [50, 59.99, 'C', 3.00, true],
        [45, 49.99, 'D', 2.00, true],
        [40, 44.99, 'E', 1.00, true],
        [0, 39.99, 'F', 0.00, false],
    ];
    foreach ($bands as [$min, $max, $letter, $point, $pass]) {
        GradeBand::create([
            'grading_scale_id' => $scale->id, 'min_score' => $min, 'max_score' => $max,
            'grade_letter' => $letter, 'grade_point' => $point, 'is_pass' => $pass,
        ]);
    }
}

it('maps scores to the configured grade band', function () {
    seedScale('NCE');
    $engine = new GradingEngine();

    expect($engine->gradeFor('NCE', 82)->grade_letter)->toBe('A')
        ->and((float) $engine->gradeFor('NCE', 82)->grade_point)->toBe(5.0)
        ->and($engine->gradeFor('NCE', 55)->grade_letter)->toBe('C')
        ->and($engine->gradeFor('NCE', 41)->grade_letter)->toBe('E');
});

it('marks a sub-pass score as failing', function () {
    seedScale('NCE');
    $band = (new GradingEngine())->gradeFor('NCE', 33);

    expect($band->grade_letter)->toBe('F')
        ->and($band->is_pass)->toBeFalse();
});
