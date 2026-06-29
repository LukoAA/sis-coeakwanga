<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Database\Factories\CurriculumCourseFactory;

class CurriculumCourse extends Model
{
    use HasFactory;

    protected $fillable = ['programme_id', 'level_id', 'course_id', 'semester', 'is_required'];

    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    protected static function newFactory(): CurriculumCourseFactory
    {
        return CurriculumCourseFactory::new();
    }
}
