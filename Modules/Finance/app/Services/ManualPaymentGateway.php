<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Str;
use Modules\Finance\Contracts\PaymentGateway;

/**
 * Records payments confirmed manually by a bursary officer. The default
 * gateway until a real provider adapter is configured.
 */
class ManualPaymentGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'manual';
    }

    public function generateReference(): string
    {
        return 'MAN-'.strtoupper(Str::random(10));
    }
}
