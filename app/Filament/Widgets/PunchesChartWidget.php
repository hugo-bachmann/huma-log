<?php

namespace App\Filament\Widgets;

use App\Models\Punch;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class PunchesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Activité des pointages';

    protected ?string $description = 'Nombre de pointages par jour · 7 derniers jours';

    protected function getData(): array
    {
        $companyId = Filament::getTenant()->id;

        $labels = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $labels[] = ucfirst($day->translatedFormat('D d/m'));
            $counts[] = Punch::query()
                ->where('company_id', $companyId)
                ->whereDate('punched_at', $day)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pointages',
                    'data' => $counts,
                    'backgroundColor' => '#2FA875',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
