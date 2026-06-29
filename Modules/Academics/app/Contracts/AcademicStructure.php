<?php

namespace Modules\Academics\Contracts;

use Illuminate\Support\Collection;

/**
 * The public contract for the Academics module. Registration and others depend
 * on THIS interface to read the level scheme and a programme's curriculum,
 * rather than reaching into Academics' models directly.
 */
interface AcademicStructure
{
    /**
     * The ordered level scheme for a programme type.
     * NCE -> {NCE1, NCE2, NCE3}; DEGREE -> {300, 400}. Schemes never mix.
     *
     * @return Collection<int, \Modules\Academics\Models\Level>
     */
    public function levelsFor(string $programmeType): Collection;

    /**
     * The curriculum (course form source) for a programme at a given level,
     * optionally narrowed to one semester. Each row carries the course plus
     * whether it is required and which semester it belongs to.
     *
     * @return Collection<int, \Modules\Academics\Models\CurriculumCourse>
     */
    public function curriculumFor(int $programmeId, int $levelId, ?int $semester = null): Collection;
}
