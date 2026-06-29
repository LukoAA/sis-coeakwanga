<?php

namespace Modules\Academics\Services;

use Illuminate\Support\Collection;
use Modules\Academics\Contracts\AcademicStructure;
use Modules\Academics\Models\CurriculumCourse;
use Modules\Academics\Models\Level;

class AcademicStructureService implements AcademicStructure
{
    public function levelsFor(string $programmeType): Collection
    {
        return Level::query()->forType($programmeType)->get();
    }

    public function curriculumFor(int $programmeId, int $levelId, ?int $semester = null): Collection
    {
        return CurriculumCourse::query()
            ->with('course')
            ->where('programme_id', $programmeId)
            ->where('level_id', $levelId)
            ->when($semester !== null, fn ($q) => $q->where('semester', $semester))
            ->get();
    }
}
