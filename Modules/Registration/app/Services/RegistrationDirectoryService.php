<?php

namespace Modules\Registration\Services;

use Illuminate\Support\Collection;
use Modules\Registration\Contracts\RegistrationDirectory;
use Modules\Registration\Models\Registration;

class RegistrationDirectoryService implements RegistrationDirectory
{
    public function isRegisteredFor(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool
    {
        return $this->approvedRegistration($enrolmentId, $sessionId, $semester)
            ?->courses()->where('course_id', $courseId)->exists() ?? false;
    }

    public function registeredCourseIds(int $enrolmentId, int $sessionId, int $semester): Collection
    {
        $registration = $this->approvedRegistration($enrolmentId, $sessionId, $semester);

        return $registration ? $registration->courses()->pluck('course_id') : collect();
    }

    private function approvedRegistration(int $enrolmentId, int $sessionId, int $semester): ?Registration
    {
        return Registration::query()
            ->where('enrolment_id', $enrolmentId)
            ->where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->where('status', Registration::STATUS_APPROVED)
            ->first();
    }
}
