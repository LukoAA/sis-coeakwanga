<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Department;
use Modules\Academics\Models\Programme;

class ProgrammeFactory extends Factory
{
    protected $model = Programme::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => 'NCE '.$this->faker->unique()->word(),
            'code' => 'PRG'.$this->faker->unique()->numberBetween(100, 999),
            'programme_type' => Programme::TYPE_NCE,
            'award' => 'Nigerian Certificate in Education',
            'duration_years' => 3,
        ];
    }

    public function degree(): static
    {
        return $this->state(fn () => [
            'name' => 'B.Ed '.$this->faker->unique()->word(),
            'programme_type' => Programme::TYPE_DEGREE,
            'award' => null, // pending affiliating university [CONFIRM]
            'duration_years' => 2,
        ]);
    }
}
