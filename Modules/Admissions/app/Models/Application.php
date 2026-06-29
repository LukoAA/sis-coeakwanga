<?php

namespace Modules\Admissions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Academics\Models\Programme;
use Modules\Academics\Models\SubjectCombination;
use Modules\Admissions\Database\Factories\ApplicationFactory;
use Modules\Identity\Models\AcademicSession;
use Modules\People\Models\Person;

class Application extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SCREENED = 'screened';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ENROLLED = 'enrolled';

    protected $fillable = [
        'person_id', 'programme_id', 'academic_session_id', 'subject_combination_id',
        'entry_route', 'jamb_reg_no', 'applicant_nce_matric', 'screening_score',
        'status', 'acceptance_fee_paid',
        'applicant_surname', 'applicant_first_name', 'applicant_other_names',
        'applicant_gender', 'applicant_dob', 'applicant_phone', 'applicant_email',
    ];

    protected function casts(): array
    {
        return [
            'screening_score' => 'decimal:2',
            'acceptance_fee_paid' => 'boolean',
            'applicant_dob' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function subjectCombination(): BelongsTo
    {
        return $this->belongsTo(SubjectCombination::class);
    }

    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class);
    }

    protected static function newFactory(): ApplicationFactory
    {
        return ApplicationFactory::new();
    }
}
