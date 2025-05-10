<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Order::query()->latest()->limit(5);
            })
            ->columns([
                            Tables\Columns\TextColumn::make('name')->label('Khách hàng'),
                            Tables\Columns\BadgeColumn::make('status')
                                ->colors([
                                    'warning' => 'pending',
                                    'success' => 'completed',
                                    'danger' => 'canceled',
                                ]),
                            Tables\Columns\TextColumn::make('created_at')->since()->label('Thời gian'),
                        ]);
    }
}
