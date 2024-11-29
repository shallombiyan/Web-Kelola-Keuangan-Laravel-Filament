<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) 
            ? Carbon::parse($this->filters['startDate']) 
            : null;

        $endDate = !is_null($this->filters['endDate'] ?? null) 
            ? Carbon::parse($this->filters['endDate']) 
            : now();

        // Apply whereBetween correctly
        $pemasukan = Transaction::incomes()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $pengeluaran = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        return [
            Stat::make('Total Pemasukan', number_format($pemasukan, 0, ',', '.')),
            Stat::make('Total Pengeluaran', number_format($pengeluaran, 0, ',', '.')),
            Stat::make('Selisih', number_format($pemasukan - $pengeluaran, 0, ',', '.')),
        ];
    }
}
