<?php

use Modules\Academics\Models\Department;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\School;
use Modules\Academics\Models\Subject;
use Modules\Academics\Models\SubjectCombination;
use Modules\Admissions\Contracts\AcceptanceFeeGate;
use Modules\Admissions\Exceptions\AcceptanceFeeUnpaidException;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Models\MatricNumberFormat;
use Modules\Admissions\Services\AdmissionService;
use Modules\Admissions\Services\InterimAcceptanceFeeGate;
use Modules\Admissions\Services\MatricNumberGenerator;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;
use Modules\People\Services\PeopleDirectoryService;

function service(): AdmissionService
{
    return new AdmissionService(
        new PeopleDirectoryService(),
        new InterimAcceptanceFeeGate(),
        new MatricNumberGenerator(),
    );
}

function degreeSetup(): array
{
    Setting::put('institution_code', 'COEA');
    MatricNumberFormat::create([
        'programme_type' => Programme::TYPE_DEGREE,
        'academic_session_id' => null,
        'pattern' => '{institution}/{year}/{school}/{major}/{serial}',
        'serial_length' => 4,
    ]);

    $session = AcademicSession::factory()->create(['name' => '2025/2026', 'starts_on' => '2025-09-01']);
    $school = School::factory()->create(['code' => 'SC']);
    $dept = Department::factory()->create(['school_id' => $school->id, 'code' => 'MTH']);
    $programme = Programme::factory()->degree()->create(['department_id' => $dept->id]);
    $major = Subject::factory()->create(['code' => 'MTH']);
    $combo = SubjectCombination::factory()->create([
        'programme_id' => $programme->id, 'major_subject_id' => $major->id, 'minor_subject_id' => null,
    ]);

    return compact('session', 'programme', 'combo');
}

it('blocks matric generation until the acceptance fee is paid', function () {
    ['session' => $session, 'programme' => $programme] = degreeSetup();

    $app = Application::factory()->create([
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'acceptance_fee_paid' => false,
    ]);

    service()->finaliseAdmission($app);
})->throws(AcceptanceFeeUnpaidException::class);

it('admits a returning NCE graduate as a second enrolment under the same person', function () {
    ['session' => $session, 'programme' => $programme, 'combo' => $combo] = degreeSetup();

    // The returning person: already exists, holds a graduated NCE enrolment.
    $person = Person::factory()->create([
        'surname' => 'Abubakar', 'first_name' => 'Musa',
        'date_of_birth' => '2001-03-14', 'phone' => '08030000001',
    ]);
    Enrolment::factory()->nce()->graduated()->for($person)->create(['matric_number' => 'NCE/2019/0421']);

    // They apply for the degree via Direct Entry, supplying their NCE matric.
    $app = Application::factory()->directEntry()->feePaid()->create([
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'subject_combination_id' => $combo->id,
        'applicant_surname' => 'Abubakar',
        'applicant_first_name' => 'Musa',
        'applicant_dob' => '2001-03-14',
        'applicant_phone' => '08030000001',
        'applicant_nce_matric' => 'NCE/2019/0421',
    ]);

    $svc = service();

    // The matcher surfaces the existing person, top-ranked.
    $matches = $svc->findReturningPersonMatches($app);
    expect($matches->first()['person']->id)->toBe($person->id);

    // Officer confirms the link; admission is finalised.
    $enrolment = $svc->finaliseAdmission($app, $matches->first()['person']);

    expect(Person::count())->toBe(1)                              // not duplicated
        ->and($person->enrolments()->count())->toBe(2)            // NCE + new Degree
        ->and($enrolment->programme_type)->toBe(Programme::TYPE_DEGREE)
        ->and($enrolment->entry_route)->toBe(Enrolment::ROUTE_DIRECT_ENTRY)
        ->and($enrolment->matric_number)->toBe('COEA/2025/SC/MTH/0001')
        ->and($app->fresh()->status)->toBe(Application::STATUS_ENROLLED);
});

it('defaults to manual offers (no auto-offer on screening)', function () {
    degreeSetup();
    $app = Application::factory()->create();

    service()->screen($app, 95);

    expect($app->fresh()->status)->toBe(Application::STATUS_SCREENED)
        ->and($app->offer)->toBeNull();
});

it('auto-offers when the intake is configured for it and the score clears the cutoff', function () {
    degreeSetup();
    Setting::put('admissions.offer_mode.UTME', 'auto');
    Setting::put('admissions.auto_offer_cutoff', 50);

    $app = Application::factory()->create(['entry_route' => Enrolment::ROUTE_UTME]);

    service()->screen($app, 75);

    expect($app->fresh()->status)->toBe(Application::STATUS_OFFERED)
        ->and($app->fresh()->offer)->not->toBeNull();
});
