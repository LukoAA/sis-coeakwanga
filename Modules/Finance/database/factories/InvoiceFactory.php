<?php

namespace Modules\Finance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\Invoice;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'enrolment_id' => Enrolment::factory(),
            'academic_session_id' => AcademicSession::factory(),
            'total' => 0,
            'status' => Invoice::STATUS_UNPAID,
        ];
    }
}
