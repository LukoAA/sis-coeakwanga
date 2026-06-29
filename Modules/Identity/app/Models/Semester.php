<?php

namespace Modules\Identity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Identity\Database\Factories\SemesterFactory;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_session_id',
        'name',
        'starts_on',
        'ends_on',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /** Scope to the single current semester. */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    protected static function newFactory(): SemesterFactory
    {
        return SemesterFactory::new();
    }
}
