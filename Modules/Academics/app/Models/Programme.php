<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academics\Database\Factories\ProgrammeFactory;

class Programme extends Model
{
    use HasFactory;

    public const TYPE_NCE = 'NCE';
    public const TYPE_DEGREE = 'DEGREE';

    protected $fillable = ['department_id', 'name', 'code', 'programme_type', 'award', 'duration_years'];

    protected function casts(): array
    {
        return ['duration_years' => 'integer'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subjectCombinations(): HasMany
    {
        return $this->hasMany(SubjectCombination::class);
    }

    public function curriculumCourses(): HasMany
    {
        return $this->hasMany(CurriculumCourse::class);
    }

    public function isNce(): bool
    {
        return $this->programme_type === self::TYPE_NCE;
    }

    protected static function newFactory(): ProgrammeFactory
    {
        return ProgrammeFactory::new();
    }
}
