<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Admissions\Models\Application;
use Modules\People\Models\Enrolment;
use Modules\People\Models\Person;

class RegistryStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active students', Enrolment::where('status', Enrolment::STATUS_ACTIVE)->count())
                ->description(Person::count().' people on record')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Applications awaiting action', Application::whereIn('status', [
                    Application::STATUS_PENDING,
                    Application::STATUS_SCREENED,
                ])->count())
                ->description('Pending or screened')
                ->color('warning')
                ->icon('heroicon-o-inbox'),

            Stat::make('Enrolled this pipeline', Application::where('status', Application::STATUS_ENROLLED)->count())
                ->description('Applications finalised')
                ->color('success')
                ->icon('heroicon-o-check-badge'),
        ];
    }
}