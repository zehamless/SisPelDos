<?php

namespace App\Filament\Widgets;

use App\Models\Periode;
use Filament\Widgets\ChartWidget;

class ChartDashboard extends ChartWidget
{
    protected static ?string $heading = 'Periode';

    protected function getData(): array
    {
        $periode = Periode::all();
        $data = [];
        $labels = [];
        foreach ($periode as $p) {
            $data[] = $p->peserta()->count();
            $labels[] = $p->tahun_ajar;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Peserta',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
