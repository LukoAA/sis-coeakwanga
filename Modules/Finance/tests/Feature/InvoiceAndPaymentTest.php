<?php

use Modules\Academics\Models\Programme;
use Modules\Finance\Models\FeeStructure;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\ManualPaymentGateway;
use Modules\Finance\Services\PaymentService;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

function freshSession(): AcademicSession
{
    return AcademicSession::factory()->create(['name' => '2025/2026', 'starts_on' => '2025-09-01']);
}

it('generates an itemized session invoice from fee structures', function () {
    $session = freshSession();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);

    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'name' => 'Tuition', 'amount' => 50000]);
    FeeStructure::factory()->sundry('Library', 7500)->create(['academic_session_id' => $session->id]);
    // Acceptance fee must NOT appear on a session invoice.
    FeeStructure::factory()->acceptance(10000)->create(['academic_session_id' => $session->id]);

    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);

    expect($invoice->items)->toHaveCount(2)
        ->and((float) $invoice->total)->toBe(57500.0)
        ->and($invoice->status)->toBe(Invoice::STATUS_UNPAID);
});

it('reduces the balance and moves unpaid -> part -> paid as payments confirm', function () {
    $session = freshSession();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);
    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'amount' => 100000]);

    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);
    $payments = new PaymentService(new ManualPaymentGateway());

    // Part payment.
    $first = $payments->recordPayment($invoice, 40000);
    $payments->confirmPayment($first);

    expect($invoice->fresh()->balance())->toBe(60000.0)
        ->and($invoice->fresh()->status)->toBe(Invoice::STATUS_PART);

    // Clear the balance.
    $second = $payments->recordPayment($invoice, 60000);
    $payments->confirmPayment($second);

    expect($invoice->fresh()->balance())->toBe(0.0)
        ->and($invoice->fresh()->status)->toBe(Invoice::STATUS_PAID);
});

it('does not count an unconfirmed (pending) payment toward the balance', function () {
    $session = freshSession();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);
    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'amount' => 50000]);

    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);
    (new PaymentService(new ManualPaymentGateway()))->recordPayment($invoice, 50000); // pending, not confirmed

    expect($invoice->fresh()->balance())->toBe(50000.0)
        ->and($invoice->fresh()->status)->toBe(Invoice::STATUS_UNPAID);
});
