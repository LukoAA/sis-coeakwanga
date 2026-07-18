<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Models\Course;
use Modules\People\Models\Enrolment;
use Modules\Identity\Models\AcademicSession;

class ScoreEntry extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VETTED = 'vetted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'enrolment_id', 'course_id', 'academic_session_id', 'semester', 'credit_units',
        'ca_score', 'exam_score', 'total', 'grade', 'grade_point', 'passed', 'status',
    ];

    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'credit_units' => 'integer',
            'ca_score' => 'decimal:2',
            'exam_score' => 'decimal:2',
            'total' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'passed' => 'boolean',
        ];
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}
