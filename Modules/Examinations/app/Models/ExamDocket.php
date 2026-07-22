<?php

namespace Modules\Examinations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Models\Course;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Enrolment;
use Modules\Examinations\Database\Factories\ExamDocketFactory;

class ExamDocket extends Model
{
    use HasFactory;

    protected $fillable = [
        'docket_number', 'enrolment_id', 'course_id', 'academic_session_id', 'semester',
        'registered', 'fee_cleared', 'attendance_ok', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'registered' => 'boolean',
            'fee_cleared' => 'boolean',
            'attendance_ok' => 'boolean',
            'issued_at' => 'datetime',
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

    protected static function newFactory(): ExamDocketFactory
    {
        return ExamDocketFactory::new();
    }
}
