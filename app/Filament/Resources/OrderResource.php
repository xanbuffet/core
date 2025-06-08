<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Dish;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'Đơn hàng';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Khách hàng')
                    ->relationship('user', 'username')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('order_no')
                    ->label('Mã đơn hàng')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->default('XAN_'.Str::random(8))
                    ->disabled(fn ($context) => $context === 'edit'),
                Forms\Components\TextInput::make('total_price')
                    ->label('Tổng tiền')
                    ->required()
                    ->numeric()
                    ->prefix('VND')
                    ->minValue(0),
                Forms\Components\Textarea::make('notes')
                    ->label('Ghi chú')
                    ->nullable()
                    ->maxLength(65535),
                Forms\Components\Textarea::make('address')
                    ->label('Địa chỉ')
                    ->nullable()
                    ->maxLength(65535),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Repeater::make('orderDishes')
                    ->relationship()
                    ->label('Danh sách món ăn')
                    ->schema([
                        Forms\Components\Select::make('dish_id')
                            ->label('Món ăn')
                            ->options(fn () => Dish::pluck('name', 'id')->lazy())
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('meal_number')
                            ->label('Suất thứ')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                    ])
                    ->grid(3)
                    ->columnSpanFull()
                    ->addActionLabel('Thêm món ăn')
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('Mã đơn hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('dishes_count')
                    ->label('Số món ăn')
                    ->counts('dishes'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('update_status')
                        ->label('Cập nhật trạng thái')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Trạng thái mới')
                                ->options([
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                ])
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->modalHeading('Xác nhận cập nhật trạng thái')
                        ->modalDescription('Bạn có chắc chắn muốn cập nhật trạng thái của đơn hàng này?')
                        ->modalSubmitActionLabel('Cập nhật')
                        ->action(function (Order $record, array $data) {
                            try {
                                $record->update([
                                    'status' => $data['status'],
                                ]);
                                \Filament\Notifications\Notification::make()
                                    ->title('Cập nhật thành công')
                                    ->body('Trạng thái đơn hàng đã được cập nhật.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Lỗi')
                                    ->body('Không thể cập nhật trạng thái: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }
}
