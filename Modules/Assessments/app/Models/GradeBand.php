<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeBand extends Model
{
    protected $fillable = [
        'grading_scale_id', 'min_score', 'max_score', 'grade_letter', 'grade_point', 'is_pass',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'is_pass' => 'boolean',
        ];
    }

    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }
}
