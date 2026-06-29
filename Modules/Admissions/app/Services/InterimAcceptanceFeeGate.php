<?php

namespace Modules\Admissions\Services;

use Modules\Admissions\Contracts\AcceptanceFeeGate;
use Modules\Admissions\Models\Application;

/**
 * Interim gate: trusts the manually-set acceptance_fee_paid flag on the
 * application. Replace the container binding with a Finance-backed gate later.
 */
class InterimAcceptanceFeeGate implements AcceptanceFeeGate
{
    public function isAcceptanceFeePaid(Application $application): bool
    {
        return (bool) $application->acceptance_fee_paid;
    }
}
