<?php

namespace Modules\Admissions\Models;

use Illuminate\Database\Eloquent\Model;

class JambImport extends Model
{
    protected $fillable = ['academic_session_id', 'file_ref', 'status', 'rows_total', 'rows_matched'];
}
