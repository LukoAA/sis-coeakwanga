<?php

namespace Modules\Assessments\Services;

use Modules\Assessments\Models\GradeBand;
use Modules\Assessments\Models\GradingScale;

/**
 * Maps a total score to a grade band for a programme type, reading the
 * configurable grade_bands. No thresholds are hard-coded here.
 */
class GradingEngine
{
    public function gradeFor(string $programmeType, float $total): ?GradeBand
    {
        $scale = GradingScale::query()->where('programme_type', $programmeType)->first();

        if (! $scale) {
            return null;
        }

        return GradeBand::query()
            ->where('grading_scale_id', $scale->id)
            ->where('min_score', '<=', $total)
            ->where('max_score', '>=', $total)
            ->first();
    }
}
