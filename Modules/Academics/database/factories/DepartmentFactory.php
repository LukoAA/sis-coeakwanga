<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Department;
use Modules\Academics\Models\School;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => 'Department of '.$this->faker->unique()->word(),
            'code' => 'DPT'.$this->faker->unique()->numberBetween(100, 999),
        ];
    }
}
