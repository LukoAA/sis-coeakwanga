<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Database\Factories\CourseFactory;

class Course extends Model
{
    use HasFactory;

    public const TYPE_CORE = 'CORE';
    public const TYPE_ELECTIVE = 'ELECTIVE';
    public const TYPE_GES = 'GES';

    protected $fillable = ['department_id', 'programme_type', 'code', 'title', 'credit_units', 'course_type'];

    protected function casts(): array
    {
        return ['credit_units' => 'integer'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** Scope to one programme type's course pool (NCE and Degree stay separate). */
    public function scopeForType(Builder $query, string $programmeType): Builder
    {
        return $query->where('programme_type', $programmeType);
    }

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }
}
