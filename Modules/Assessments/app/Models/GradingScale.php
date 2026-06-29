<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScale extends Model
{
    protected $fillable = ['programme_type', 'name'];

    public function bands(): HasMany
    {
        return $this->hasMany(GradeBand::class);
    }
}
