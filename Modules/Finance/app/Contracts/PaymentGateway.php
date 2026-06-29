<?php

namespace Modules\Finance\Contracts;

/**
 * Abstraction over a payment provider. A real Remita/Paystack/Flutterwave
 * adapter implements this; for now ManualPaymentGateway records officer-
 * confirmed payments. Swapping providers is a single container binding.
 */
interface PaymentGateway
{
    /** Provider key stored on the payment ('manual', 'remita', ...). */
    public function key(): string;

    /** A unique provider reference for a new payment. */
    public function generateReference(): string;
}
