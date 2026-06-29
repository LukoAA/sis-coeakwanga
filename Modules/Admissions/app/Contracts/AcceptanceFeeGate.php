<?php

namespace Modules\Admissions\Contracts;

use Modules\Admissions\Models\Application;

/**
 * Decides whether an application's acceptance fee has been paid. Admissions
 * checks this BEFORE generating a matric number / creating an enrolment.
 *
 * For now an interim implementation reads a manually-set flag on the
 * application. When the Finance module is built, bind a real implementation
 * that checks payment status — Admissions code does not change.
 */
interface AcceptanceFeeGate
{
    public function isAcceptanceFeePaid(Application $application): bool;
}
