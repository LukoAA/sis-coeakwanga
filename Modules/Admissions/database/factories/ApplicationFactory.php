<?php

namespace Modules\Admissions\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Programme;
use Modules\Admissions\Models\Application;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'programme_id' => Programme::factory(),
            'academic_session_id' => AcademicSession::factory(),
            'entry_route' => Enrolment::ROUTE_UTME,
            'status' => Application::STATUS_PENDING,
            'acceptance_fee_paid' => false,
            'applicant_surname' => $this->faker->lastName(),
            'applicant_first_name' => $this->faker->firstName(),
            'applicant_gender' => $this->faker->randomElement(['male', 'female']),
            'applicant_dob' => $this->faker->dateTimeBetween('-30 years', '-16 years')->format('Y-m-d'),
            'applicant_phone' => $this->faker->unique()->numerify('080########'),
        ];
    }

    public function directEntry(): static
    {
        return $this->state(fn () => ['entry_route' => Enrolment::ROUTE_DIRECT_ENTRY]);
    }

    public function feePaid(): static
    {
        return $this->state(fn () => ['acceptance_fee_paid' => true]);
    }
}
