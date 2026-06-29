<?php

namespace Modules\Academics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\Subject;
use Modules\Academics\Models\SubjectCombination;

class SubjectCombinationFactory extends Factory
{
    protected $model = SubjectCombination::class;

    public function definition(): array
    {
        return [
            'programme_id' => Programme::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'major_subject_id' => Subject::factory(),
            'minor_subject_id' => Subject::factory(),
        ];
    }
}
