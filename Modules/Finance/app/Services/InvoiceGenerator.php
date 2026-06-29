<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Modules\Admissions\Models\Application;
use Modules\Finance\Models\FeeStructure;
use Modules\Finance\Models\Invoice;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;

class InvoiceGenerator
{
    /**
     * Build an itemized session invoice for an enrolment from the fee structures
     * matching its programme type, programme, and level (acceptance excluded).
     */
    public function generateSessionInvoice(Enrolment $enrolment, AcademicSession $session): Invoice
    {
        return DB::transaction(function () use ($enrolment, $session) {
            $structures = FeeStructure::query()
                ->where('academic_session_id', $session->id)
                ->where('programme_type', $enrolment->programme_type)
                ->where('fee_type', '!=', FeeStructure::TYPE_ACCEPTANCE)
                ->where(fn ($q) => $q->whereNull('programme_id')->orWhere('programme_id', $enrolment->programme_id))
                ->where(fn ($q) => $q->whereNull('level_id')->orWhere('level_id', $enrolment->current_level_id))
                ->get();

            $invoice = Invoice::firstOrCreate(
                ['enrolment_id' => $enrolment->id, 'academic_session_id' => $session->id],
                ['total' => 0, 'status' => Invoice::STATUS_UNPAID],
            );

            $this->fillItems($invoice, $structures);

            return $invoice;
        });
    }

    /**
     * Build the acceptance-fee invoice for an application (before the student
     * exists). This is what the FinanceAcceptanceFeeGate checks.
     */
    public function generateAcceptanceInvoice(Application $application): Invoice
    {
        return DB::transaction(function () use ($application) {
            $structures = FeeStructure::query()
                ->where('academic_session_id', $application->academic_session_id)
                ->where('programme_type', $application->programme->programme_type)
                ->where('fee_type', FeeStructure::TYPE_ACCEPTANCE)
                ->get();

            $invoice = Invoice::firstOrCreate(
                ['application_id' => $application->id],
                ['academic_session_id' => $application->academic_session_id, 'total' => 0, 'status' => Invoice::STATUS_UNPAID],
            );

            $this->fillItems($invoice, $structures);

            return $invoice;
        });
    }

    private function fillItems(Invoice $invoice, $structures): void
    {
        foreach ($structures as $structure) {
            $invoice->items()->firstOrCreate(
                ['fee_structure_id' => $structure->id],
                ['description' => $structure->name, 'amount' => $structure->amount],
            );
        }

        $invoice->update(['total' => $invoice->items()->sum('amount')]);
        $invoice->refreshStatus();
    }
}
