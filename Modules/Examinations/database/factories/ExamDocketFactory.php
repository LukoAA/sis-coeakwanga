<?php

namespace Modules\Examinations\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academics\Models\Course;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Examinations\Models\ExamDocket;

class ExamDocketFactory extends Factory
{
    protected $model = ExamDocket::class;

    public function definition(): array
    {
        return [
            'docket_number' => 'DKT/'.$this->faker->unique()->numerify('####/######/AB'),
            'enrolment_id' => Enrolment::factory(),
            'course_id' => Course::factory(),
            'academic_session_id' => AcademicSession::factory(),
            'semester' => 1,
            'registered' => true,
            'fee_cleared' => true,
            'attendance_ok' => true,
            'issued_at' => now(),
        ];
    }
}
