<?php

namespace Modules\Examinations\Services;

use Modules\Examinations\Contracts\ExaminationsDirectory;
use Modules\Examinations\Models\ExamDocket;

class ExaminationsDirectoryService implements ExaminationsDirectory
{
    public function hasValidDocket(int $enrolmentId, int $courseId, int $sessionId, int $semester): bool
    {
        return ExamDocket::query()
            ->where('enrolment_id', $enrolmentId)
            ->where('course_id', $courseId)
            ->where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->whereNotNull('issued_at')
            ->exists();
    }
}
