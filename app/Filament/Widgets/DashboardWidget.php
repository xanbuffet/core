<?php

namespace App\Filament\Widgets;

use App\Models\Dish;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('🍽 Tổng món ăn', Dish::count()),
            Stat::make('🧾 Tổng đơn hàng', Order::count()),
        ];
    }
}
