<?php

namespace Modules\Assessments\Services;

use Modules\Assessments\Models\Classification;

class ClassificationService
{
    public function classify(string $programmeType, float $cgpa): ?string
    {
        return Classification::query()
            ->where('programme_type', $programmeType)
            ->where('min_cgpa', '<=', $cgpa)
            ->where('max_cgpa', '>=', $cgpa)
            ->value('label');
    }
}
