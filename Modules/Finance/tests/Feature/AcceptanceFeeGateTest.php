<?php

use Modules\Academics\Models\Department;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\School;
use Modules\Admissions\Models\Application;
use Modules\Finance\Models\FeeStructure;
use Modules\Finance\Services\FinanceAcceptanceFeeGate;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\ManualPaymentGateway;
use Modules\Finance\Services\PaymentService;
use Modules\Identity\Models\AcademicSession;

function applicationForGate(): Application
{
    $session = AcademicSession::factory()->create();
    $school = School::factory()->create();
    $dept = Department::factory()->create(['school_id' => $school->id]);
    $programme = Programme::factory()->create(['department_id' => $dept->id, 'programme_type' => Programme::TYPE_NCE]);

    FeeStructure::factory()->acceptance(10000)->create([
        'academic_session_id' => $session->id,
        'programme_type' => Programme::TYPE_NCE,
    ]);

    return Application::factory()->create([
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
    ]);
}

it('reports acceptance fee unpaid until the acceptance invoice is fully paid', function () {
    $application = applicationForGate();
    $gate = new FinanceAcceptanceFeeGate();

    // No invoice yet -> not paid.
    expect($gate->isAcceptanceFeePaid($application))->toBeFalse();

    $invoice = (new InvoiceGenerator())->generateAcceptanceInvoice($application);

    // Invoice exists but unpaid -> still not paid.
    expect($gate->isAcceptanceFeePaid($application))->toBeFalse();

    $payments = new PaymentService(new ManualPaymentGateway());
    $payments->confirmPayment($payments->recordPayment($invoice, 10000));

    // Fully paid -> gate opens.
    expect($gate->isAcceptanceFeePaid($application->fresh()))->toBeTrue();
});
