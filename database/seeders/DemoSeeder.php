<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Academics\Models\Course;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\SubjectCombination;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Services\AdmissionService;
use Modules\Admissions\Services\MatricNumberGenerator;
use Modules\Assessments\Models\ScoreEntry;
use Modules\Assessments\Services\ResultWorkflow;
use Modules\Assessments\Services\ScoreEntryService;
use Modules\Finance\Services\InvoiceGenerator;
use Modules\Finance\Services\PaymentService;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

/**
 * Demo-readiness seeder: a realistic cohort routed through the REAL services
 * (matric generator, invoice generator, score entry) so counters, invoices,
 * and grades are provably consistent with live behaviour.
 *
 * Idempotent: guarded by a marker person; safe to re-run.
 *
 * Run:  php artisan db:seed --class=DemoSeeder
 *
 * Requires the module seeders to have run first (sessions, programmes,
 * combinations, fee structures, grading scales):
 *   php artisan module:seed Identity
 *   php artisan module:seed Academics
 *   php artisan module:seed Admissions
 *   php artisan module:seed Finance
 *   php artisan module:seed Assessments
 */
class DemoSeeder extends Seeder
{
    private const MARKER_PHONE = '08000000999'; // idempotency sentinel

    /** Realistic Nigerian names for the cohort. */
    private array $surnames = [
        'Abdullahi', 'Adamu', 'Agbo', 'Akpan', 'Aliyu', 'Amadi', 'Audu', 'Ayuba',
        'Bello', 'Danjuma', 'Egwu', 'Eze', 'Garba', 'Ibrahim', 'Idris', 'Igwe',
        'James', 'Kambai', 'Lawal', 'Madaki', 'Musa', 'Nweke', 'Obi', 'Ogah',
        'Okafor', 'Okon', 'Onyeka', 'Sani', 'Suleiman', 'Tanko', 'Umar', 'Usman',
        'Wada', 'Yakubu', 'Yusuf', 'Zakari',
    ];

    private array $firstNamesMale = [
        'Adamu', 'Bala', 'Chinedu', 'Danladi', 'Emeka', 'Habu', 'Ismail', 'John',
        'Kabiru', 'Luka', 'Mohammed', 'Nuhu', 'Peter', 'Samuel', 'Tijani', 'Yohanna',
    ];

    private array $firstNamesFemale = [
        'Aisha', 'Blessing', 'Charity', 'Deborah', 'Esther', 'Fatima', 'Grace',
        'Hadiza', 'Joy', 'Ladi', 'Mary', 'Ngozi', 'Patience', 'Rahila', 'Talatu', 'Zainab',
    ];

    public function run(): void
    {
        // ---- Idempotency guard -------------------------------------------
        if (Person::where('phone', self::MARKER_PHONE)->exists()) {
            $this->command?->warn('DemoSeeder already ran (marker found). Skipping.');

            return;
        }

        // ---- Preconditions ------------------------------------------------
        $session = AcademicSession::where('is_current', true)->first();
        $nceProgrammes = Programme::where('programme_type', Programme::TYPE_NCE)->get();
        $nce1 = Level::where('programme_type', Programme::TYPE_NCE)->where('code', 'NCE1')->first();

        if (! $session || $nceProgrammes->isEmpty() || ! $nce1) {
            $this->command?->error('Missing base data. Run the module seeders first (Identity, Academics, Admissions, Finance, Assessments).');

            return;
        }

        $matric = app(MatricNumberGenerator::class);
        $invoices = app(InvoiceGenerator::class);
        $payments = app(PaymentService::class);
        $scores = app(ScoreEntryService::class);
        $workflow = app(ResultWorkflow::class);
        $admissions = app(AdmissionService::class);

        $phoneCounter = 100;
        $nextPhone = function () use (&$phoneCounter): string {
            return '0803'.str_pad((string) $phoneCounter++, 7, '0', STR_PAD_LEFT);
        };

        // ==================================================================
        // 1) ~30 active NCE students, matrics via the REAL generator
        // ==================================================================
        $this->command?->info('Seeding active NCE students...');
        $activeEnrolments = collect();

        foreach (range(1, 30) as $i) {
            $gender = $i % 2 === 0 ? 'male' : 'female';
            $programme = $nceProgrammes[$i % $nceProgrammes->count()];
            $combination = SubjectCombination::where('programme_id', $programme->id)->first();

            $person = Person::create([
                'surname' => $this->surnames[$i % count($this->surnames)],
                'first_name' => $gender === 'male'
                    ? $this->firstNamesMale[$i % count($this->firstNamesMale)]
                    : $this->firstNamesFemale[$i % count($this->firstNamesFemale)],
                'gender' => $gender,
                'date_of_birth' => sprintf('%d-%02d-%02d', 2004 - ($i % 6), ($i % 12) + 1, ($i % 27) + 1),
                'phone' => $nextPhone(),
                'state_of_origin' => 'Nasarawa',
                'lga' => 'Akwanga',
            ]);

            // Route matric through the real generator via a throwaway
            // application context so serials stay in sync.
            $application = Application::create([
                'person_id' => $person->id,
                'programme_id' => $programme->id,
                'academic_session_id' => $session->id,
                'subject_combination_id' => $combination?->id,
                'entry_route' => Enrolment::ROUTE_UTME,
                'status' => Application::STATUS_ENROLLED,
                'acceptance_fee_paid' => true,
                'applicant_surname' => $person->surname,
                'applicant_first_name' => $person->first_name,
                'applicant_gender' => $person->gender,
                'applicant_dob' => $person->date_of_birth,
                'applicant_phone' => $person->phone,
            ]);

            $enrolment = $person->enrolments()->create([
                'matric_number' => $matric->generate($application),
                'programme_type' => Programme::TYPE_NCE,
                'entry_route' => Enrolment::ROUTE_UTME,
                'status' => Enrolment::STATUS_ACTIVE,
                'admission_session_id' => $session->id,
                'programme_id' => $programme->id,
                'current_level_id' => $nce1->id,
                'subject_combination_id' => $combination?->id,
            ]);

            $activeEnrolments->push($enrolment);
        }

        // ==================================================================
        // 2) 5 graduated NCE holders (returning-graduate demo fodder)
        // ==================================================================
        $this->command?->info('Seeding graduated NCE holders...');

        foreach (range(1, 5) as $i) {
            $person = Person::create([
                'surname' => $this->surnames[($i + 15) % count($this->surnames)],
                'first_name' => $this->firstNamesMale[($i + 3) % count($this->firstNamesMale)],
                'gender' => 'male',
                'date_of_birth' => sprintf('%d-0%d-1%d', 1998 + $i, ($i % 8) + 1, $i),
                'phone' => $nextPhone(),
                'state_of_origin' => 'Nasarawa',
                'lga' => 'Akwanga',
            ]);

            $person->enrolments()->create([
                'matric_number' => sprintf('NCE/2019/%04d', 500 + $i), // historic scheme
                'programme_type' => Programme::TYPE_NCE,
                'entry_route' => Enrolment::ROUTE_UTME,
                'status' => Enrolment::STATUS_GRADUATED,
                'graduation_outcome' => ['Distinction', 'Upper Credit', 'Upper Credit', 'Lower Credit', 'Merit'][$i - 1],
                'graduated_at' => '2022-07-31',
                'programme_id' => $nceProgrammes->first()->id,
            ]);
        }

        // ==================================================================
        // 3) Applications across every pipeline stage
        // ==================================================================
        $this->command?->info('Seeding pipeline applications...');

        $stages = [
            Application::STATUS_PENDING, Application::STATUS_PENDING, Application::STATUS_PENDING,
            Application::STATUS_SCREENED, Application::STATUS_SCREENED,
            Application::STATUS_OFFERED, Application::STATUS_OFFERED,
            Application::STATUS_ACCEPTED,
            Application::STATUS_REJECTED,
        ];

        foreach ($stages as $i => $status) {
            $programme = $nceProgrammes[$i % $nceProgrammes->count()];
            $gender = $i % 2 === 0 ? 'female' : 'male';

            $application = $admissions->submitApplication([
                'programme_id' => $programme->id,
                'academic_session_id' => $session->id,
                'entry_route' => Enrolment::ROUTE_UTME,
                'applicant_surname' => $this->surnames[($i + 7) % count($this->surnames)],
                'applicant_first_name' => $gender === 'male'
                    ? $this->firstNamesMale[($i + 5) % count($this->firstNamesMale)]
                    : $this->firstNamesFemale[($i + 5) % count($this->firstNamesFemale)],
                'applicant_gender' => $gender,
                'applicant_dob' => sprintf('2005-%02d-%02d', ($i % 12) + 1, ($i % 25) + 1),
                'applicant_phone' => $nextPhone(),
            ]);

            if (in_array($status, [Application::STATUS_SCREENED, Application::STATUS_OFFERED, Application::STATUS_ACCEPTED])) {
                $admissions->screen($application, 45 + ($i * 5));
            }
            if (in_array($status, [Application::STATUS_OFFERED, Application::STATUS_ACCEPTED])) {
                $admissions->makeOffer($application->fresh());
            }
            if ($status === Application::STATUS_ACCEPTED) {
                $offer = $application->fresh()->offer;
                $admissions->acceptOffer($offer);
                $invoices->generateAcceptanceInvoice($application->fresh());
            }
            if ($status === Application::STATUS_REJECTED) {
                $application->update(['status' => Application::STATUS_REJECTED]);
            }
        }

        // ==================================================================
        // 4) Session invoices: unpaid / part / paid via real services
        // ==================================================================
        $this->command?->info('Seeding invoices and payments...');

        foreach ($activeEnrolments->take(24) as $i => $enrolment) {
            $invoice = $invoices->generateSessionInvoice($enrolment, $session);

            if ($invoice->total <= 0) {
                continue;
            }

            if ($i % 3 === 1) {        // a third part-paid (50%)
                $payments->confirmPayment($payments->recordPayment($invoice, round($invoice->total / 2, 2)));
            } elseif ($i % 3 === 2) {  // a third fully paid
                $payments->confirmPayment($payments->recordPayment($invoice, (float) $invoice->total));
            }
            // remaining third stays unpaid
        }

        // ==================================================================
        // 5) Score entries across every workflow state (incl. published fails)
        // ==================================================================
        $this->command?->info('Seeding score entries...');

        $course = Course::where('programme_type', Programme::TYPE_NCE)->first();

        if ($course) {
            // Passing scores at each workflow stage.
            $plan = [
                'draft' => 5, 'submitted' => 4, 'vetted' => 3, 'approved' => 3, 'published' => 8,
            ];
            $cursor = 0;

            foreach ($plan as $stage => $count) {
                foreach (range(1, $count) as $j) {
                    $enrolment = $activeEnrolments[$cursor % $activeEnrolments->count()];
                    $cursor++;

                    $ca = 15 + (($cursor * 3) % 15);       // 15–29
                    $exam = 30 + (($cursor * 7) % 40);     // 30–69

                    $entry = $scores->enterScore($enrolment, $course, $session, 1, $ca, $exam);

                    if (in_array($stage, ['submitted', 'vetted', 'approved', 'published'])) {
                        $entry = $workflow->submit($entry);
                    }
                    if (in_array($stage, ['vetted', 'approved', 'published'])) {
                        $entry = $workflow->vet($entry);
                    }
                    if (in_array($stage, ['approved', 'published'])) {
                        $entry = $workflow->approve($entry);
                    }
                    if ($stage === 'published') {
                        $workflow->publish($entry);
                    }
                }
            }

            // Two published FAILS -> demonstrable carry-overs.
            foreach ($activeEnrolments->slice(26, 2) as $enrolment) {
                $entry = $scores->enterScore($enrolment, $course, $session, 1, 8.0, 15.0); // total 23 -> F
                $workflow->publish($workflow->approve($workflow->vet($workflow->submit($entry))));
            }
        }

        // ---- Marker person (idempotency sentinel) ------------------------
        Person::create([
            'surname' => 'DemoSeeder',
            'first_name' => 'Marker',
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'phone' => self::MARKER_PHONE,
        ]);

        $this->command?->info('DemoSeeder complete: ~40 people, full pipeline, invoices, and results seeded.');
    }
}