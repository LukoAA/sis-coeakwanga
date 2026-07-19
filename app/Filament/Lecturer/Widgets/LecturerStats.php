<?php

namespace App\Filament\Lecturer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Assessments\Models\ScoreEntry;

class LecturerStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Draft results', ScoreEntry::where('status', ScoreEntry::STATUS_DRAFT)->count())
                ->icon('heroicon-o-pencil'),

            Stat::make('Awaiting approval', ScoreEntry::whereIn('status', [
                    ScoreEntry::STATUS_SUBMITTED,
                    ScoreEntry::STATUS_VETTED,
                ])->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Published', ScoreEntry::where('status', ScoreEntry::STATUS_PUBLISHED)->count())
                ->color('success')
                ->icon('heroicon-o-globe-alt'),
        ];
    }
}