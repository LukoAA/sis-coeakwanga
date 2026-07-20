<?php

namespace Modules\Registration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academics\Models\Course;

class RegistrationCourse extends Model
{
    protected $fillable = ['registration_id', 'course_id', 'is_carry_over'];

    protected function casts(): array
    {
        return ['is_carry_over' => 'boolean'];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
