<?php

namespace Modules\Registration\Exceptions;

use RuntimeException;

class FeeNotClearedException extends RuntimeException
{
    public static function forEnrolment(int $enrolmentId): self
    {
        return new self("Enrolment #{$enrolmentId} has not met the fee-clearance threshold; cannot submit registration.");
    }
}
