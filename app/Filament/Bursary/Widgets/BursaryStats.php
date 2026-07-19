<?php

namespace App\Filament\Bursary\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;

class BursaryStats extends BaseWidget
{
    protected function getStats(): array
    {
        $invoiced = (float) Invoice::sum('total');
        $collected = (float) Payment::where('status', Payment::STATUS_CONFIRMED)->sum('amount');

        return [
            Stat::make('Total invoiced', '₦'.number_format($invoiced, 2))
                ->icon('heroicon-o-document-text'),

            Stat::make('Collected', '₦'.number_format($collected, 2))
                ->description($invoiced > 0 ? number_format($collected / $invoiced * 100, 1).'% of invoiced' : '—')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Unpaid invoices', Invoice::where('status', Invoice::STATUS_UNPAID)->count())
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}