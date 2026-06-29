<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Course;
use Modules\Academics\Models\Department;
use Modules\Academics\Models\Programme;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'programme_type' => Programme::TYPE_NCE,
            'code' => 'CRS'.$this->faker->unique()->numberBetween(100, 999),
            'title' => $this->faker->sentence(3),
            'credit_units' => $this->faker->numberBetween(1, 4),
            'course_type' => Course::TYPE_CORE,
        ];
    }

    public function degree(): static
    {
        return $this->state(fn () => ['programme_type' => Programme::TYPE_DEGREE]);
    }
}
