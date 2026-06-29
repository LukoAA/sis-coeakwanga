<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultApproval extends Model
{
    protected $fillable = ['score_entry_id', 'stage', 'actor_id', 'acted_at'];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function scoreEntry(): BelongsTo
    {
        return $this->belongsTo(ScoreEntry::class);
    }
}
