<?php

namespace Modules\Admissions\Models;

use Illuminate\Database\Eloquent\Model;

class MatricNumberFormat extends Model
{
    protected $fillable = ['programme_type', 'academic_session_id', 'pattern', 'serial_length'];

    protected function casts(): array
    {
        return ['serial_length' => 'integer'];
    }
}
