<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Identity\Models\AcademicSession;

class CourseAllocation extends Model
{
    protected $fillable = ['course_id', 'lecturer_id', 'academic_session_id', 'semester'];

    protected function casts(): array
    {
        return ['semester' => 'integer'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
