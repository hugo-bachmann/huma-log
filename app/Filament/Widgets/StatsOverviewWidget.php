<?php

namespace App\Filament\Widgets;

use App\Enums\OffPeriodeStatus;
use App\Models\Employee;
use App\Models\OffPeriode;
use App\Models\Punch;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $companyId = Filament::getTenant()->id;
        $today = today();

        $punchesToday = Punch::query()
            ->where('company_id', $companyId)
            ->whereDate('punched_at', $today)
            ->count();

        $activeEmployees = Employee::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->count();

        $anomaliesToday = Punch::query()
            ->where('company_id', $companyId)
            ->where('is_suspect', true)
            ->whereDate('punched_at', $today)
            ->count();

        $pendingAbsences = OffPeriode::query()
            ->where('company_id', $companyId)
            ->where('status', OffPeriodeStatus::Pending)
            ->count();

        return [
            Stat::make('Pointages aujourd\'hui', $punchesToday),
            Stat::make('Employés actifs', $activeEmployees),
            Stat::make('Anomalies (aujourd\'hui)', $anomaliesToday)
                ->color($anomaliesToday > 0 ? 'warning' : 'success'),
            Stat::make('Absences en attente', $pendingAbsences)
                ->color($pendingAbsences > 0 ? 'warning' : 'gray'),
        ];
    }
}
