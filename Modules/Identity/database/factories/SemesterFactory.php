<?php

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Semester;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'academic_session_id' => AcademicSession::factory(),
            'name' => $this->faker->randomElement(['First', 'Second']),
            'starts_on' => null,
            'ends_on' => null,
            'is_current' => false,
        ];
    }

    public function current(): static
    {
        return $this->state(fn () => ['is_current' => true]);
    }
}
