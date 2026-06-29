<?php

namespace Modules\Finance\Contracts;

/**
 * The fee-clearance gate that Registration and Examinations consume. Clearance
 * is a configurable percentage of the session invoice — a different threshold
 * for registering vs sitting exams.
 */
interface FeeClearance
{
    public function percentPaid(int $enrolmentId, int $sessionId): float;

    public function isClearedToRegister(int $enrolmentId, int $sessionId): bool;

    public function isClearedForExams(int $enrolmentId, int $sessionId): bool;
}
