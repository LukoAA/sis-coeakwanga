<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Subject;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => 'SUB'.$this->faker->unique()->numberBetween(100, 999),
        ];
    }
}
