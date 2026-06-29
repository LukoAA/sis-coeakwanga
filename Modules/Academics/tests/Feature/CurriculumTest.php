<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\CurriculumCourse;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Services\AcademicStructureService;

it('returns the curriculum (course form source) for a programme and level', function () {
    $structure = new AcademicStructureService();

    $programme = Programme::factory()->create();
    $level = Level::factory()->create(['code' => 'NCE1', 'rank' => 1]);

    $first = Course::factory()->create();
    $second = Course::factory()->create();

    CurriculumCourse::factory()->create([
        'programme_id' => $programme->id, 'level_id' => $level->id,
        'course_id' => $first->id, 'semester' => 1,
    ]);
    CurriculumCourse::factory()->create([
        'programme_id' => $programme->id, 'level_id' => $level->id,
        'course_id' => $second->id, 'semester' => 2,
    ]);

    $all = $structure->curriculumFor($programme->id, $level->id);
    $firstSemester = $structure->curriculumFor($programme->id, $level->id, 1);

    expect($all)->toHaveCount(2)
        ->and($firstSemester)->toHaveCount(1)
        ->and($firstSemester->first()->course->id)->toBe($first->id);
});
