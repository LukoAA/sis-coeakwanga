<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Academics\Models\Course;
use Modules\Assessments\Filament\Resources\ScoreEntries\ScoreEntryResource;
use Modules\Assessments\Services\ScoreEntryService;
use Modules\Identity\Contracts\AcademicContext;
use Modules\People\Models\Enrolment;

class CreateScoreEntry extends CreateRecord
{
    protected static string $resource = ScoreEntryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $context = app(AcademicContext::class);
        $session = $context->currentSession();
        $semester = $context->currentSemester();

        abort_if(! $session || ! $semester, 422, 'No current academic session/semester is set. Ask the registrar to set one in Identity.');

        return app(ScoreEntryService::class)->enterScore(
            Enrolment::findOrFail($data['enrolment_id']),
            Course::findOrFail($data['course_id']),
            $session,
            (int) filter_var($semester->name, FILTER_SANITIZE_NUMBER_INT) ?: ($semester->name === 'First' ? 1 : 2),
            (float) $data['ca_score'],
            (float) $data['exam_score'],
        );
    }
}