<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = ['programme_type', 'min_cgpa', 'max_cgpa', 'label'];

    protected function casts(): array
    {
        return ['min_cgpa' => 'decimal:2', 'max_cgpa' => 'decimal:2'];
    }
}
