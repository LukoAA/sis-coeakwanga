<?php

namespace Modules\Identity\Services;

use Illuminate\Support\Facades\DB;
use Modules\Identity\Contracts\AcademicContext;
use Modules\Identity\Models\AcademicSession;
use Modules\Identity\Models\Semester;

class AcademicContextService implements AcademicContext
{
    public function currentSession(): ?AcademicSession
    {
        return AcademicSession::query()->current()->first();
    }

    public function currentSemester(): ?Semester
    {
        return Semester::query()->current()->first();
    }

    public function setCurrentSession(AcademicSession $session): void
    {
        DB::transaction(function () use ($session) {
            AcademicSession::query()
                ->where('is_current', true)
                ->whereKeyNot($session->getKey())
                ->update(['is_current' => false]);

            $session->forceFill(['is_current' => true])->save();
        });
    }

    public function setCurrentSemester(Semester $semester): void
    {
        DB::transaction(function () use ($semester) {
            Semester::query()
                ->where('is_current', true)
                ->whereKeyNot($semester->getKey())
                ->update(['is_current' => false]);

            $semester->forceFill(['is_current' => true])->save();
        });
    }
}
