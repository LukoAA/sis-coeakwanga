<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Course;
use Modules\Academics\Models\CurriculumCourse;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;

class CurriculumCourseFactory extends Factory
{
    protected $model = CurriculumCourse::class;

    public function definition(): array
    {
        return [
            'programme_id' => Programme::factory(),
            'level_id' => Level::factory(),
            'course_id' => Course::factory(),
            'semester' => 1,
            'is_required' => true,
        ];
    }
}
