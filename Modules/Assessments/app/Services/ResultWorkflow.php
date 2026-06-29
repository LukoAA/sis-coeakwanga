<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\DB;
use Modules\Assessments\Models\ResultApproval;
use Modules\Assessments\Models\ScoreEntry;

/**
 * Advances a score entry through the approval chain:
 * draft -> submitted (lecturer) -> vetted (HOD/exams) -> approved (Board of
 * Examiners / Academic Board) -> published. Each transition is audited.
 */
class ResultWorkflow
{
    private const ORDER = [
        ScoreEntry::STATUS_DRAFT => 0,
        ScoreEntry::STATUS_SUBMITTED => 1,
        ScoreEntry::STATUS_VETTED => 2,
        ScoreEntry::STATUS_APPROVED => 3,
        ScoreEntry::STATUS_PUBLISHED => 4,
    ];

    public function submit(ScoreEntry $entry, ?int $actorId = null): ScoreEntry
    {
        return $this->advance($entry, ScoreEntry::STATUS_SUBMITTED, $actorId);
    }

    public function vet(ScoreEntry $entry, ?int $actorId = null): ScoreEntry
    {
        return $this->advance($entry, ScoreEntry::STATUS_VETTED, $actorId);
    }

    public function approve(ScoreEntry $entry, ?int $actorId = null): ScoreEntry
    {
        return $this->advance($entry, ScoreEntry::STATUS_APPROVED, $actorId);
    }

    public function publish(ScoreEntry $entry, ?int $actorId = null): ScoreEntry
    {
        return $this->advance($entry, ScoreEntry::STATUS_PUBLISHED, $actorId);
    }

    private function advance(ScoreEntry $entry, string $toStage, ?int $actorId): ScoreEntry
    {
        return DB::transaction(function () use ($entry, $toStage, $actorId) {
            $entry->update(['status' => $toStage]);

            ResultApproval::create([
                'score_entry_id' => $entry->id,
                'stage' => $toStage,
                'actor_id' => $actorId,
                'acted_at' => now(),
            ]);

            return $entry->fresh();
        });
    }
}
