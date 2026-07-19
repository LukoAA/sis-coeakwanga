<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Assessments\Models\ResultSummary;
use Modules\Finance\Contracts\FeeClearance;
use Modules\Identity\Contracts\AcademicContext;
use Modules\People\Models\Enrolment;

class StudentOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $person = auth()->user()?->person_id;
        $enrolment = Enrolment::where('person_id', $person)
            ->where('status', Enrolment::STATUS_ACTIVE)
            ->latest()
            ->first();

        if (! $enrolment) {
            return [Stat::make('No active enrolment', '—')
                ->description('Contact the Registry if this is unexpected')];
        }

        $session = app(AcademicContext::class)->currentSession();
        $percentPaid = $session
            ? app(FeeClearance::class)->percentPaid($enrolment->id, $session->id)
            : 0.0;

        $cgpa = ResultSummary::where('enrolment_id', $enrolment->id)
            ->latest()->value('cgpa');

        return [
            Stat::make('Matric number', $enrolment->matric_number)
                ->description(($enrolment->programme?->name ?? '').' — '.($enrolment->level?->label ?? ''))
                ->icon('heroicon-o-identification'),

            Stat::make('Fees paid', number_format($percentPaid, 1).'%')
                ->description($percentPaid >= 100 ? 'Fully cleared' : 'Outstanding balance — see My Fees')
                ->color($percentPaid >= 100 ? 'success' : ($percentPaid >= 50 ? 'warning' : 'danger'))
                ->icon('heroicon-o-banknotes'),

            Stat::make('CGPA', $cgpa !== null ? number_format((float) $cgpa, 2) : '—')
                ->description($cgpa !== null ? 'From published results' : 'No results published yet')
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}