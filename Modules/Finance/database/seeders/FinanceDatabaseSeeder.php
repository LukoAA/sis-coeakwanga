<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academics\Models\Programme;
use Modules\Finance\Models\FeeStructure;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;

class FinanceDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('finance.clearance.register_threshold', 50);  // % paid to register
        Setting::put('finance.clearance.exams_threshold', 100);    // % paid to sit exams
        Setting::put('finance.default_gateway', 'manual');

        $session = AcademicSession::query()->where('is_current', true)->first()
            ?? AcademicSession::query()->first();

        if (! $session) {
            return;
        }

        foreach ([Programme::TYPE_NCE, Programme::TYPE_DEGREE] as $type) {
            FeeStructure::query()->updateOrCreate(
                ['name' => 'Acceptance Fee', 'programme_type' => $type, 'academic_session_id' => $session->id, 'fee_type' => FeeStructure::TYPE_ACCEPTANCE],
                ['amount' => 10000],
            );
            FeeStructure::query()->updateOrCreate(
                ['name' => 'Tuition', 'programme_type' => $type, 'academic_session_id' => $session->id, 'fee_type' => FeeStructure::TYPE_TUITION],
                ['amount' => $type === Programme::TYPE_DEGREE ? 90000 : 50000],
            );
            FeeStructure::query()->updateOrCreate(
                ['name' => 'Library & Sundry', 'programme_type' => $type, 'academic_session_id' => $session->id, 'fee_type' => FeeStructure::TYPE_SUNDRY],
                ['amount' => 7500],
            );
        }
    }
}
