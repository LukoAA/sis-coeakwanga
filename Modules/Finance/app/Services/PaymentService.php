<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Modules\Finance\Contracts\PaymentGateway;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;

class PaymentService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /** Record a pending payment against an invoice. */
    public function recordPayment(Invoice $invoice, float $amount): Payment
    {
        return $invoice->payments()->create([
            'enrolment_id' => $invoice->enrolment_id,
            'gateway' => $this->gateway->key(),
            'reference' => $this->gateway->generateReference(),
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    /**
     * Confirm a payment (officer action for manual; webhook/verify for real
     * gateways) and recompute the invoice status — all in one transaction so a
     * payment and the balance it changes can never drift apart.
     */
    public function confirmPayment(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update(['status' => Payment::STATUS_CONFIRMED, 'paid_at' => now()]);
            $payment->invoice->refreshStatus();

            return $payment;
        });
    }
}
