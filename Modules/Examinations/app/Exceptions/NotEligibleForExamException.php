<?php

namespace Modules\Examinations\Exceptions;

use RuntimeException;

class NotEligibleForExamException extends RuntimeException
{
    /** @param array<int, string> $reasons */
    public function __construct(public readonly array $reasons)
    {
        parent::__construct('Not eligible for exam: '.implode('; ', $reasons));
    }
}
