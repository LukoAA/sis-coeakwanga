<?php

namespace Modules\Registration\Services;

use Illuminate\Support\Facades\DB;
use Modules\Academics\Contracts\AcademicStructure;
use Modules\Academics\Models\Course;
use Modules\Academics\Models\Level;
use Modules\Finance\Contracts\FeeClearance;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Setting;
use Modules\People\Models\Enrolment;
use Modules\Registration\Contracts\CarryOverProvider;
use Modules\Registration\Exceptions\FeeNotClearedException;
use Modules\Registration\Models\Registration;
use Modules\Registration\Models\RegistrationCourse;

class RegistrationService
{
    public function __construct(
        private readonly AcademicStructure $structure,
        private readonly FeeClearance $clearance,
        private readonly CarryOverProvider $carryOvers,
    ) {}

    /**
     * Open (or return) a draft course form. Auto-fills the required curriculum
     * courses and any carry-overs; the student adds electives themselves. A
     * draft can be built without paying — only SUBMIT is gated.
     */
    public function openCourseForm(Enrolment $enrolment, AcademicSession $session, int $semester, Level $level): Registration
    {
        return DB::transaction(function () use ($enrolment, $session, $semester, $level) {
            $registration = Registration::firstOrCreate(
                [
                    'enrolment_id' => $enrolment->id,
                    'academic_session_id' => $session->id,
                    'semester' => $semester,
                ],
                ['level_id' => $level->id, 'status' => Registration::STATUS_DRAFT, 'fee_cleared' => false],
            );

            if ($registration->isDraft() && $registration->courses()->count() === 0) {
                foreach ($this->structure->curriculumFor($enrolment->programme_id, $level->id, $semester) as $entry) {
                    if ($entry->is_required) {
                        $registration->courses()->create(['course_id' => $entry->course_id, 'is_carry_over' => false]);
                    }
                }

                foreach ($this->carryOvers->carryOverCourseIds($enrolment->id) as $courseId) {
                    $registration->courses()->firstOrCreate(['course_id' => $courseId], ['is_carry_over' => true]);
                }
            }

            return $registration->fresh('courses');
        });
    }

    /** Add an elective. Allowed only while add-drop is open and the course is in the curriculum. */
    public function addElective(Registration $registration, Course $course): RegistrationCourse
    {
        $this->assertAddDropOpen();

        $inCurriculum = $this->structure
            ->curriculumFor($registration->enrolment->programme_id, $registration->level_id, $registration->semester)
            ->contains('course_id', $course->id);

        abort_unless($inCurriculum, 422, 'Course is not in this level\'s curriculum.');

        return $registration->courses()->firstOrCreate(['course_id' => $course->id], ['is_carry_over' => false]);
    }

    public function dropCourse(Registration $registration, Course $course): void
    {
        $this->assertAddDropOpen();
        $registration->courses()->where('course_id', $course->id)->delete();
    }

    /**
     * Submit the course form. GATED on fee clearance. Snapshots fee_cleared and
     * total_units, then auto-approves if configured and validation passes.
     */
    public function submit(Registration $registration): Registration
    {
        if (! $this->clearance->isClearedToRegister($registration->enrolment_id, $registration->academic_session_id)) {
            throw FeeNotClearedException::forEnrolment($registration->enrolment_id);
        }

        return DB::transaction(function () use ($registration) {
            $registration->update([
                'status' => Registration::STATUS_SUBMITTED,
                'fee_cleared' => true,
                'total_units' => $this->totalUnits($registration),
            ]);

            if ($this->approvalMode() === 'auto' && $this->validate($registration) === []) {
                $this->approve($registration->fresh(), null);
            }

            return $registration->fresh();
        });
    }

    public function approve(Registration $registration, ?int $approverId = null): Registration
    {
        $registration->update([
            'status' => Registration::STATUS_APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        return $registration->fresh();
    }

    /** @return array<int, string> human-readable validation issues (empty = valid) */
    public function validate(Registration $registration): array
    {
        $issues = [];
        $units = $this->totalUnits($registration);

        $min = (int) Setting::get('registration.min_units', 0);
        $max = (int) Setting::get('registration.max_units', 24);

        if ($units < $min) {
            $issues[] = "Total units ({$units}) below minimum ({$min}).";
        }
        if ($units > $max) {
            $issues[] = "Total units ({$units}) above maximum ({$max}).";
        }
        if (! $this->clearance->isClearedToRegister($registration->enrolment_id, $registration->academic_session_id)) {
            $issues[] = 'Fee clearance threshold not met.';
        }

        return $issues;
    }

    private function totalUnits(Registration $registration): int
    {
        $courseIds = $registration->courses()->pluck('course_id');

        return (int) Course::query()->whereIn('id', $courseIds)->sum('credit_units');
    }

    private function approvalMode(): string
    {
        return Setting::get('registration.approval_mode', 'manual');
    }

    private function assertAddDropOpen(): void
    {
        abort_unless((bool) Setting::get('registration.add_drop_open', true), 422, 'Add-drop window is closed.');
    }
}
