<?php

namespace Modules\Admissions\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admissions\Contracts\AcceptanceFeeGate;
use Modules\Admissions\Exceptions\AcceptanceFeeUnpaidException;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Models\Offer;
use Modules\Identity\Models\Setting;
use Modules\People\Contracts\PeopleDirectory;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

/**
 * Orchestrates the admission flow: application -> (match returning person) ->
 * screen -> offer -> accept -> (fee gate) -> matric + enrolment.
 */
class AdmissionService
{
    public function __construct(
        private readonly PeopleDirectory $directory,
        private readonly AcceptanceFeeGate $feeGate,
        private readonly MatricNumberGenerator $matric,
    ) {}

    public function submitApplication(array $data): Application
    {
        return Application::create($data + ['status' => Application::STATUS_PENDING]);
    }

    /**
     * Surface existing people who may be this applicant (returning NCE graduate).
     * The admissions officer confirms before linking — never auto-merges.
     */
    public function findReturningPersonMatches(Application $application): Collection
    {
        return $this->directory->findPotentialMatches([
            'matric_number' => $application->applicant_nce_matric,
            'surname' => $application->applicant_surname,
            'first_name' => $application->applicant_first_name,
            'date_of_birth' => $application->applicant_dob?->toDateString(),
            'phone' => $application->applicant_phone,
        ]);
    }

    public function screen(Application $application, float $score): Application
    {
        $application->update([
            'screening_score' => $score,
            'status' => Application::STATUS_SCREENED,
        ]);

        // Auto-offer only where the intake is configured for it; default is manual.
        if ($this->offerMode($application->entry_route) === 'auto'
            && $score >= $this->autoOfferCutoff()) {
            $this->makeOffer($application);
        }

        return $application->fresh();
    }

    public function makeOffer(Application $application): Offer
    {
        $application->update(['status' => Application::STATUS_OFFERED]);

        return $application->offer()->updateOrCreate([], [
            'status' => Offer::STATUS_PENDING,
            'offered_at' => now(),
        ]);
    }

    public function acceptOffer(Offer $offer): Offer
    {
        $offer->update(['status' => Offer::STATUS_ACCEPTED, 'accepted_at' => now()]);
        $offer->application->update(['status' => Application::STATUS_ACCEPTED]);

        return $offer;
    }

    /**
     * Finalise admission: gated on acceptance-fee payment. Generates the matric
     * number and creates the enrolment against the existing or newly-created
     * person. The returning-graduate path passes $existingPerson.
     */
    public function finaliseAdmission(Application $application, ?Person $existingPerson = null): Enrolment
    {
        if (! $this->feeGate->isAcceptanceFeePaid($application)) {
            throw AcceptanceFeeUnpaidException::forApplication($application->id);
        }

        return DB::transaction(function () use ($application, $existingPerson) {
            $person = $existingPerson
                ?? $application->person
                ?? $this->createPersonFromApplication($application);

            $matricNumber = $this->matric->generate($application);

            $enrolment = $person->enrolments()->create([
                'matric_number' => $matricNumber,
                'programme_type' => $application->programme->programme_type,
                'entry_route' => $application->entry_route,
                'status' => Enrolment::STATUS_ACTIVE,
                'admission_session_id' => $application->academic_session_id,
                'programme_id' => $application->programme_id,
                'subject_combination_id' => $application->subject_combination_id,
            ]);

            $application->update([
                'person_id' => $person->id,
                'status' => Application::STATUS_ENROLLED,
            ]);

            return $enrolment;
        });
    }

    private function createPersonFromApplication(Application $application): Person
    {
        return $this->directory->createPerson([
            'surname' => $application->applicant_surname,
            'first_name' => $application->applicant_first_name,
            'other_names' => $application->applicant_other_names,
            'gender' => $application->applicant_gender,
            'date_of_birth' => $application->applicant_dob,
            'phone' => $application->applicant_phone,
            'email' => $application->applicant_email,
        ]);
    }

    private function offerMode(string $entryRoute): string
    {
        return Setting::get("admissions.offer_mode.{$entryRoute}", 'manual');
    }

    private function autoOfferCutoff(): float
    {
        return (float) Setting::get('admissions.auto_offer_cutoff', 50);
    }
}
