<?php

namespace Modules\People\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Database\Factories\EnrolmentFactory;

/**
 * One person's admission into one programme. Owns its matric number, level
 * progression, and graduation outcome. A person may hold several over time
 * (e.g. an NCE admission, then later a Degree admission).
 */
class Enrolment extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Programme types — drive which level scheme applies (never a global enum).
    public const TYPE_NCE = 'NCE';
    public const TYPE_DEGREE = 'DEGREE';

    // Entry routes.
    public const ROUTE_UTME = 'UTME';
    public const ROUTE_DIRECT_ENTRY = 'DIRECT_ENTRY';

    // Statuses.
    public const STATUS_ACTIVE = 'active';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_DEFERRED = 'deferred';

    protected $fillable = [
        'person_id',
        'matric_number',
        'programme_type',
        'entry_route',
        'status',
        'graduation_outcome',
        'graduated_at',
        'admission_session_id',
        'programme_id',
        'current_level_id',
        'subject_combination_id',
    ];

    protected function casts(): array
    {
        return [
            'graduated_at' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function admissionSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'admission_session_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeGraduated(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_GRADUATED);
    }

    public function isNce(): bool
    {
        return $this->programme_type === self::TYPE_NCE;
    }

    public function isDegree(): bool
    {
        return $this->programme_type === self::TYPE_DEGREE;
    }

    protected static function newFactory(): EnrolmentFactory
    {
        return EnrolmentFactory::new();
    }
}
