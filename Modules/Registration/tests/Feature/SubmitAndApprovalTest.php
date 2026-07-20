<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\CurriculumCourse;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Services\AcademicStructureService;
use Modules\Finance\Models\FeeStructure;
use Modules\Finance\Services\FeeClearanceService;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\ManualPaymentGateway;
use Modules\Finance\Services\PaymentService;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;
use Modules\People\Models\Enrolment;
use Modules\Registration\Exceptions\FeeNotClearedException;
use Modules\Registration\Models\Registration;
use Modules\Registration\Services\InterimCarryOverProvider;
use Modules\Registration\Services\RegistrationService;

function regService(): RegistrationService
{
    return new RegistrationService(
        new AcademicStructureService(),
        new FeeClearanceService(),
        new InterimCarryOverProvider(),
    );
}

function draftFor(AcademicSession $session): array
{
    Setting::put('finance.clearance.register_threshold', 50);
    Setting::put('registration.min_units', 0);
    Setting::put('registration.max_units', 24);

    $programme = Programme::factory()->create();
    $level = Level::factory()->create(['code' => 'NCE1', 'rank' => 1]);
    $core = Course::factory()->create(['credit_units' => 3]);
    CurriculumCourse::factory()->create(['programme_id' => $programme->id, 'level_id' => $level->id, 'course_id' => $core->id, 'semester' => 1, 'is_required' => true]);

    $enrolment = Enrolment::factory()->nce()->create([
        'programme_id' => $programme->id, 'current_level_id' => $level->id, 'admission_session_id' => $session->id,
    ]);

    $registration = regService()->openCourseForm($enrolment, $session, 1, $level);

    return compact('enrolment', 'registration');
}

function payPercent(Enrolment $enrolment, AcademicSession $session, float $percent): void
{
    FeeStructure::factory()->create(['academic_session_id' => $session->id, 'amount' => 100000]);
    $invoice = (new InvoiceGenerator())->generateSessionInvoice($enrolment, $session);
    $payments = new PaymentService(new ManualPaymentGateway());
    $payments->confirmPayment($payments->recordPayment($invoice, 100000 * $percent / 100));
}

it('blocks submission when the fee-clearance threshold is not met', function () {
    $session = AcademicSession::factory()->create();
    ['registration' => $registration] = draftFor($session);

    regService()->submit($registration); // nothing paid
})->throws(FeeNotClearedException::class);

it('allows submission once part-payment clears the threshold (manual approval stays pending)', function () {
    $session = AcademicSession::factory()->create();
    ['enrolment' => $enrolment, 'registration' => $registration] = draftFor($session);
    Setting::put('registration.approval_mode', 'manual');

    payPercent($enrolment, $session, 50);

    $submitted = regService()->submit($registration);

    expect($submitted->status)->toBe(Registration::STATUS_SUBMITTED) // not auto-approved
        ->and($submitted->fee_cleared)->toBeTrue()
        ->and($submitted->total_units)->toBe(3);
});

it('auto-approves on submit when approval mode is auto and rules pass', function () {
    $session = AcademicSession::factory()->create();
    ['enrolment' => $enrolment, 'registration' => $registration] = draftFor($session);
    Setting::put('registration.approval_mode', 'auto');

    payPercent($enrolment, $session, 50);

    $submitted = regService()->submit($registration);

    expect($submitted->status)->toBe(Registration::STATUS_APPROVED)
        ->and($submitted->approved_at)->not->toBeNull();
});

it('approves a submitted registration manually', function () {
    $session = AcademicSession::factory()->create();
    ['enrolment' => $enrolment, 'registration' => $registration] = draftFor($session);
    Setting::put('registration.approval_mode', 'manual');
    payPercent($enrolment, $session, 50);

    $svc = regService();
    $submitted = $svc->submit($registration);
    $approved = $svc->approve($submitted);

    expect($approved->status)->toBe(Registration::STATUS_APPROVED);
});
