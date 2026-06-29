<?php

use Modules\Finance\Models\FeeStructure;
use Modules\Finance\Services\FeeClearanceService;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\ManualPaymentGateway;
use Modules\Finance\Services\PaymentService;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;
use Modules\People\Models\Enrolment;

beforeEach(function () {
    Setting::put('finance.clearance.register_threshold', 50);
    Setting::put('finance.clearance.exams_threshold', 100);
    $this->clearance = new FeeClearanceService();
});

it('clears for registration at the part-payment threshold but not for exams', function () {
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);
    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'amount' => 100000]);

    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);
    $payments = new PaymentService(new ManualPaymentGateway());
    $payments->confirmPayment($payments->recordPayment($invoice, 50000)); // exactly 50%

    expect($this->clearance->percentPaid($enrolment->id, $session->id))->toBe(50.0)
        ->and($this->clearance->isClearedToRegister($enrolment->id, $session->id))->toBeTrue()
        ->and($this->clearance->isClearedForExams($enrolment->id, $session->id))->toBeFalse();
});

it('clears for exams only at full payment', function () {
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);
    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'amount' => 100000]);

    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);
    $payments = new PaymentService(new ManualPaymentGateway());
    $payments->confirmPayment($payments->recordPayment($invoice, 100000));

    expect($this->clearance->isClearedForExams($enrolment->id, $session->id))->toBeTrue();
});

it('treats a student with no invoice as not cleared', function () {
    $session = AcademicSession::factory()->create();
    $enrolment = Enrolment::factory()->nce()->create(['admission_session_id' => $session->id]);

    expect($this->clearance->isClearedToRegister($enrolment->id, $session->id))->toBeFalse();
});
