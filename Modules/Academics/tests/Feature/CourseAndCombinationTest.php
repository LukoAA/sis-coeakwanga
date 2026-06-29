<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\Subject;
use Modules\Academics\Models\SubjectCombination;

it('keeps NCE and Degree course pools separate', function () {
    Course::factory()->count(3)->create();              // NCE by default
    Course::factory()->degree()->count(2)->create();    // Degree

    expect(Course::forType(Programme::TYPE_NCE)->count())->toBe(3)
        ->and(Course::forType(Programme::TYPE_DEGREE)->count())->toBe(2);
});

it('builds a subject combination from catalog subjects', function () {
    $programme = Programme::factory()->create();
    $maths = Subject::factory()->create(['name' => 'Mathematics']);
    $science = Subject::factory()->create(['name' => 'Integrated Science']);

    $combo = SubjectCombination::factory()->create([
        'programme_id' => $programme->id,
        'name' => 'Mathematics / Integrated Science',
        'major_subject_id' => $maths->id,
        'minor_subject_id' => $science->id,
    ]);

    expect($combo->majorSubject->name)->toBe('Mathematics')
        ->and($combo->minorSubject->name)->toBe('Integrated Science')
        ->and($combo->programme->id)->toBe($programme->id);
});

it('allows a single-subject combination (minor optional)', function () {
    $combo = SubjectCombination::factory()->create(['minor_subject_id' => null]);

    expect($combo->minor_subject_id)->toBeNull()
        ->and($combo->majorSubject)->not->toBeNull();
});
