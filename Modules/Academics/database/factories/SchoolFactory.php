<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\School;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => 'School of '.$this->faker->unique()->word(),
            'code' => 'SCH'.$this->faker->unique()->numberBetween(100, 999),
        ];
    }
}
