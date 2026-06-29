<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\People\Models\Enrolment;

class ResultSummary extends Model
{
    protected $fillable = [
        'enrolment_id', 'academic_session_id', 'semester',
        'tcu', 'tgp', 'gpa', 'cgpa', 'classification',
    ];

    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'tcu' => 'integer',
            'tgp' => 'decimal:2',
            'gpa' => 'decimal:2',
            'cgpa' => 'decimal:2',
        ];
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }
}
