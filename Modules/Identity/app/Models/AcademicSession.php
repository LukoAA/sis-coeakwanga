<?php

namespace Modules\Identity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Identity\Database\Factories\AcademicSessionFactory;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    /** Scope to the single current session. */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    protected static function newFactory(): AcademicSessionFactory
    {
        return AcademicSessionFactory::new();
    }
}
