<?php

use Modules\Academics\Models\Course;
use Modules\Academics\Models\Department;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\School;
use Modules\Academics\Models\Subject;
use Modules\Academics\Models\SubjectCombination;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Models\MatricNumberFormat;
use Modules\Admissions\Services\MatricNumberGenerator;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;

function nceSetup(): array
{
    Setting::put('institution_code', 'COEA');
    MatricNumberFormat::create([
        'programme_type' => Programme::TYPE_NCE,
        'academic_session_id' => null,
        'pattern' => '{institution}/{year}/{school}/{major}/{minor}/{serial}',
        'serial_length' => 4,
    ]);

    $session = AcademicSession::factory()->create(['name' => '2022/2023', 'starts_on' => '2022-09-01']);
    $school = School::factory()->create(['code' => 'SC']);
    $dept = Department::factory()->create(['school_id' => $school->id, 'code' => 'CSC']);
    $programme = Programme::factory()->create(['department_id' => $dept->id, 'programme_type' => Programme::TYPE_NCE]);
    $major = Subject::factory()->create(['code' => 'CSC']);
    $minor = Subject::factory()->create(['code' => 'ECO']);
    $combo = SubjectCombination::factory()->create([
        'programme_id' => $programme->id,
        'major_subject_id' => $major->id,
        'minor_subject_id' => $minor->id,
    ]);

    return compact('session', 'programme', 'combo');
}

it('generates a matric number in the configured COEA format', function () {
    ['session' => $session, 'programme' => $programme, 'combo' => $combo] = nceSetup();

    $app = Application::factory()->create([
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'subject_combination_id' => $combo->id,
    ]);

    $matric = (new MatricNumberGenerator())->generate($app);

    expect($matric)->toBe('COEA/2022/SC/CSC/ECO/0001');
});

it('increments the serial per programme and session', function () {
    ['session' => $session, 'programme' => $programme, 'combo' => $combo] = nceSetup();
    $gen = new MatricNumberGenerator();

    $first = $gen->generate(Application::factory()->create([
        'programme_id' => $programme->id, 'academic_session_id' => $session->id, 'subject_combination_id' => $combo->id,
    ]));
    $second = $gen->generate(Application::factory()->create([
        'programme_id' => $programme->id, 'academic_session_id' => $session->id, 'subject_combination_id' => $combo->id,
    ]));

    expect($first)->toEndWith('/0001')
        ->and($second)->toEndWith('/0002');
});
