<?php

namespace Modules\People\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

class EnrolmentFactory extends Factory
{
    protected $model = Enrolment::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'matric_number' => $this->faker->unique()->numerify('NCE/2025/####'),
            'programme_type' => Enrolment::TYPE_NCE,
            'entry_route' => Enrolment::ROUTE_UTME,
            'status' => Enrolment::STATUS_ACTIVE,
        ];
    }

    public function nce(): static
    {
        return $this->state(fn () => [
            'programme_type' => Enrolment::TYPE_NCE,
            'entry_route' => Enrolment::ROUTE_UTME,
            'matric_number' => $this->faker->unique()->numerify('NCE/2025/####'),
        ]);
    }

    public function degree(): static
    {
        return $this->state(fn () => [
            'programme_type' => Enrolment::TYPE_DEGREE,
            'entry_route' => Enrolment::ROUTE_DIRECT_ENTRY,
            'matric_number' => $this->faker->unique()->numerify('DEG/2025/####'),
        ]);
    }

    public function graduated(): static
    {
        return $this->state(fn () => [
            'status' => Enrolment::STATUS_GRADUATED,
            'graduation_outcome' => 'Merit',
            'graduated_at' => now()->subYear()->toDateString(),
        ]);
    }
}
