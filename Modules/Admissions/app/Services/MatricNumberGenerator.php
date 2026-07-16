<?php

namespace Modules\Admissions\Services;

use Illuminate\Support\Facades\DB;
use Modules\Academics\Models\Programme;
use Modules\Admissions\Models\Application;
use Modules\Admissions\Models\MatricNumberFormat;
use Modules\Admissions\Models\MatricSerial;
use Modules\Identity\Models\Setting;

/**
 * Builds a matriculation number from a configurable pattern, e.g.
 *   COEA/2022/SC/CSC/ECO/0233
 * Tokens: {institution} {year} {school} {major} {minor} {serial}
 * Degree patterns simply omit {minor} (no double-major). Serial is allocated
 * atomically per programme + session.
 */
class MatricNumberGenerator
{
    public function generate(Application $application): string
    {
        $programme = $application->programme()->with('department.school')->first();
        $session = $application->academicSession;
        $combination = $application->subjectCombination()->with(['majorSubject', 'minorSubject'])->first();

        $format = $this->formatFor($programme->programme_type, $application->academic_session_id);
        $serial = $this->nextSerial($application->programme_id, $application->academic_session_id);

        $year = $session?->starts_on?->format('Y') ?? substr((string) $session?->name, 0, 4);

        $tokens = [
            '{institution}' => Setting::get('institution_code', 'COEA'),
            '{year}' => $year,
            '{school}' => $programme->department?->school?->code ?? '',
            '{major}' => $combination?->majorSubject?->code ?? '',
            '{minor}' => $combination?->minorSubject?->code ?? '',
            '{serial}' => str_pad((string) $serial, $format->serial_length, '0', STR_PAD_LEFT),
        ];

        // Collapse any double slashes left by an absent token (e.g. degree minor).
        return preg_replace('#/+#', '/', strtr($format->pattern, $tokens));
    }

    private function formatFor(string $programmeType, int $sessionId): MatricNumberFormat
    {
        return MatricNumberFormat::query()
            ->where('programme_type', $programmeType)
            ->where(fn ($q) => $q->where('academic_session_id', $sessionId)->orWhereNull('academic_session_id'))
            ->orderByRaw('academic_session_id is null') // session-specific wins over default
            ->firstOrFail();
    }

    private function nextSerial(int $programmeId, int $sessionId): int
    {
        return DB::transaction(function () use ($programmeId, $sessionId) {
            $row = MatricSerial::query()
                ->where('programme_id', $programmeId)
                ->where('academic_session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                $row = MatricSerial::create([
                    'programme_id' => $programmeId,
                    'academic_session_id' => $sessionId,
                    'last_serial' => 0,
                ]);
            }

            // Guard against desync: never issue a serial at or below what already
            // exists for this programme + session (enrolments created outside the
            // generator, resets, rolled-back runs, etc.).
            $highestUsed = \Modules\People\Models\Enrolment::query()
                ->where('programme_id', $programmeId)
                ->where('admission_session_id', $sessionId)
                ->count();

            if ($row->last_serial < $highestUsed) {
                $row->last_serial = $highestUsed;
            }

            $row->increment('last_serial');

            return $row->last_serial;
        });
    }
}
