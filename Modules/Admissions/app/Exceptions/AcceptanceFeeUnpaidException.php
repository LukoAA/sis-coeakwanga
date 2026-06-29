<?php

namespace Modules\Admissions\Exceptions;

use RuntimeException;

class AcceptanceFeeUnpaidException extends RuntimeException
{
    public static function forApplication(int $applicationId): self
    {
        return new self("Acceptance fee not paid for application #{$applicationId}; cannot generate matric number.");
    }
}
