<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Semester;
use Modules\Identity\Models\Setting;
use Modules\Identity\Services\AcademicContextService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->context = new AcademicContextService();
});

it('returns the current session and semester', function () {
    $session = AcademicSession::factory()->current()->create();
    $semester = Semester::factory()->current()->for($session)->create();

    expect($this->context->currentSession()?->id)->toBe($session->id)
        ->and($this->context->currentSemester()?->id)->toBe($semester->id);
});

it('returns null when no period is current', function () {
    AcademicSession::factory()->create();   // not current

    expect($this->context->currentSession())->toBeNull()
        ->and($this->context->currentSemester())->toBeNull();
});

it('keeps only one semester current when a new one is set', function () {
    $session = AcademicSession::factory()->current()->create();
    $old = Semester::factory()->current()->for($session)->state(['name' => 'First'])->create();
    $new = Semester::factory()->for($session)->state(['name' => 'Second'])->create();

    $this->context->setCurrentSemester($new);

    expect($new->fresh()->is_current)->toBeTrue()
        ->and($old->fresh()->is_current)->toBeFalse()
        ->and(Semester::query()->current()->count())->toBe(1);
});


it('round-trips settings of different types', function () {
    Setting::put('attendance_eligibility_threshold', 75);
    Setting::put('add_drop_open', false);
    Setting::put('grade_bands', ['A' => 70, 'B' => 60]);

    expect(Setting::get('attendance_eligibility_threshold'))->toBe(75)
        ->and(Setting::get('add_drop_open'))->toBeFalse()
        ->and(Setting::get('grade_bands'))->toBe(['A' => 70, 'B' => 60])
        ->and(Setting::get('missing_key', 'fallback'))->toBe('fallback');
});
