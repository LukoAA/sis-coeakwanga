<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\CurriculumCourse;
use Modules\Academics\Models\Level;
use Modules\Academics\Models\Programme;
use Modules\Academics\Services\AcademicStructureService;
use Modules\Finance\Services\FeeClearanceService;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Registration\Contracts\CarryOverProvider;
use Modules\Registration\Services\InterimCarryOverProvider;
use Modules\Registration\Services\RegistrationService;

function buildCurriculum(): array
{
    $session = AcademicSession::factory()->create();
    $programme = Programme::factory()->create();
    $level = Level::factory()->create(['code' => 'NCE1', 'rank' => 1]);

    $core1 = Course::factory()->create(['credit_units' => 3]);
    $core2 = Course::factory()->create(['credit_units' => 2]);
    $elective = Course::factory()->create(['credit_units' => 2]);

    CurriculumCourse::factory()->create(['programme_id' => $programme->id, 'level_id' => $level->id, 'course_id' => $core1->id, 'semester' => 1, 'is_required' => true]);
    CurriculumCourse::factory()->create(['programme_id' => $programme->id, 'level_id' => $level->id, 'course_id' => $core2->id, 'semester' => 1, 'is_required' => true]);
    CurriculumCourse::factory()->create(['programme_id' => $programme->id, 'level_id' => $level->id, 'course_id' => $elective->id, 'semester' => 1, 'is_required' => false]);

    $enrolment = Enrolment::factory()->nce()->create([
        'programme_id' => $programme->id,
        'current_level_id' => $level->id,
        'admission_session_id' => $session->id,
    ]);

    return compact('session', 'programme', 'level', 'core1', 'core2', 'elective', 'enrolment');
}

function service(CarryOverProvider $carry = null): RegistrationService
{
    return new RegistrationService(
        new AcademicStructureService(),
        new FeeClearanceService(),
        $carry ?? new InterimCarryOverProvider(),
    );
}

it('auto-fills required courses but not electives', function () {
    ['session' => $s, 'level' => $l, 'enrolment' => $e, 'core1' => $c1, 'core2' => $c2, 'elective' => $el] = buildCurriculum();

    $registration = service()->openCourseForm($e, $s, 1, $l);

    $registered = $registration->courses->pluck('course_id');
    expect($registered)->toContain($c1->id)
        ->and($registered)->toContain($c2->id)
        ->and($registered)->not->toContain($el->id)   // elective NOT auto-added
        ->and($registration->courses)->toHaveCount(2);
});

it('includes carry-over courses from the provider', function () {
    ['session' => $s, 'level' => $l, 'enrolment' => $e] = buildCurriculum();
    $carried = Course::factory()->create();

    $provider = new class($carried->id) implements CarryOverProvider {
        public function __construct(private int $id) {}
        public function carryOverCourseIds(int $enrolmentId): array { return [$this->id]; }
    };

    $registration = service($provider)->openCourseForm($e, $s, 1, $l);

    $carryOver = $registration->courses->firstWhere('course_id', $carried->id);
    expect($carryOver)->not->toBeNull()
        ->and($carryOver->is_carry_over)->toBeTrue();
});

it('lets a student add an elective from the curriculum', function () {
    ['session' => $s, 'level' => $l, 'enrolment' => $e, 'elective' => $el] = buildCurriculum();

    $svc = service();
    $registration = $svc->openCourseForm($e, $s, 1, $l);
    $svc->addElective($registration, $el);

    expect($registration->fresh()->courses->pluck('course_id'))->toContain($el->id);
});
