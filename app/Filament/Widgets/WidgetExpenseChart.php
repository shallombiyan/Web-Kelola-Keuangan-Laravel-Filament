<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Chart';
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) 
            ? Carbon::parse($this->filters['startDate']) 
            : null;

        $endDate = !is_null($this->filters['endDate'] ?? null) 
            ? Carbon::parse($this->filters['endDate']) 
            : now();

        $data = Trend::model(Transaction::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Daily expenses',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
