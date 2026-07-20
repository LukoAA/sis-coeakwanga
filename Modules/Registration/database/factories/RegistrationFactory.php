<?php

namespace Modules\Registration\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Level;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Registration\Models\Registration;

class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'enrolment_id' => Enrolment::factory(),
            'academic_session_id' => AcademicSession::factory(),
            'semester' => 1,
            'level_id' => Level::factory(),
            'status' => Registration::STATUS_DRAFT,
            'fee_cleared' => false,
        ];
    }
}
