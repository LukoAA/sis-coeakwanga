<?php

namespace Modules\Finance\Services;

use Modules\Finance\Contracts\FeeClearance;
use Modules\Finance\Models\Invoice;
use Modules\Identity\Models\Setting;

class FeeClearanceService implements FeeClearance
{
    public function percentPaid(int $enrolmentId, int $sessionId): float
    {
        $invoice = Invoice::query()
            ->where('enrolment_id', $enrolmentId)
            ->where('academic_session_id', $sessionId)
            ->first();

        return $invoice ? $invoice->percentPaid() : 0.0;
    }

    public function isClearedToRegister(int $enrolmentId, int $sessionId): bool
    {
        return $this->percentPaid($enrolmentId, $sessionId)
            >= (float) Setting::get('finance.clearance.register_threshold', 50);
    }

    public function isClearedForExams(int $enrolmentId, int $sessionId): bool
    {
        return $this->percentPaid($enrolmentId, $sessionId)
            >= (float) Setting::get('finance.clearance.exams_threshold', 100);
    }
}
