<?php

namespace App\Filament\Widgets;

use App\Models\Periode;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ChartDashboard extends ChartWidget
{
    protected static ?string $heading = 'Periode';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $periode = Periode::all();
        $data = [];
        $labels = [];
        foreach ($periode as $p) {
            $data[] = $p->peserta()->count();
            $labels[] = $p->tahun;
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

  protected function getOptions(): RawJs
{
    return RawJs::make(<<<JS
    {
        scales: {
            y: {
                ticks: {
                    precision: 0
                }
            }
        }
    }
JS
    );
}
}
