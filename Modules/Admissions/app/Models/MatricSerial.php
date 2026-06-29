<?php

namespace Modules\Admissions\Models;

use Illuminate\Database\Eloquent\Model;

class MatricSerial extends Model
{
    protected $fillable = ['programme_id', 'academic_session_id', 'last_serial'];

    protected function casts(): array
    {
        return ['last_serial' => 'integer'];
    }
}
