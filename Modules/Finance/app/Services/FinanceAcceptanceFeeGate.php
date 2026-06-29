<?php

namespace Modules\Finance\Services;

use Modules\Admissions\Contracts\AcceptanceFeeGate;
use Modules\Admissions\Models\Application;
use Modules\Finance\Models\Invoice;

/**
 * The REAL acceptance-fee gate, backed by Finance payment data. Bind this in
 * place of Admissions' InterimAcceptanceFeeGate and Admissions starts gating
 * matric generation on an actually-paid acceptance invoice — no Admissions
 * code changes, just the binding.
 */
class FinanceAcceptanceFeeGate implements AcceptanceFeeGate
{
    public function isAcceptanceFeePaid(Application $application): bool
    {
        $invoice = Invoice::query()->where('application_id', $application->id)->first();

        return $invoice ? $invoice->percentPaid() >= 100.0 : false;
    }
}
