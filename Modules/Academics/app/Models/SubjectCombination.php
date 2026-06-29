<?php

namespace Modules\Academics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Database\Factories\SubjectCombinationFactory;

class SubjectCombination extends Model
{
    use HasFactory;

    protected $fillable = ['programme_id', 'name', 'major_subject_id', 'minor_subject_id'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function majorSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'major_subject_id');
    }

    public function minorSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'minor_subject_id');
    }

    protected static function newFactory(): SubjectCombinationFactory
    {
        return SubjectCombinationFactory::new();
    }
}
