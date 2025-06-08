<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Người dùng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->label('Số điện thoại')
                    ->required()
                    ->unique(table: User::class, column: 'username', ignoreRecord: true)
                    ->maxLength(10)
                    ->minLength(10)
                    ->regex('/^[0-9]{10}$/')
                    ->placeholder('Nhập số điện thoại 10 chữ số')
                    ->helperText('Ví dụ: 0987654321'),
                Forms\Components\TextInput::make('address')
                    ->label('Địa chỉ')
                    ->nullable()
                    ->placeholder('Nhập địa chỉ của người dùng')
                    ->helperText('Ví dụ: 123 Đường Láng, Hà Nội'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('SĐT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')
                    ->label('Số đơn hàng')
                    ->sortable()
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Mật Khẩu')
                        ->icon('heroicon-o-key')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Xác nhận reset mật khẩu')
                        ->modalDescription('Bạn có chắc chắn muốn reset mật khẩu của người dùng này về "12345678"?')
                        ->modalSubmitActionLabel('Xác nhận')
                        ->action(function (User $record) {
                            $record->update(['password' => '12345678']);
                            \Filament\Notifications\Notification::make()
                                ->title('Mật khẩu đã được reset')
                                ->body('Mật khẩu của người dùng đã được đặt lại thành "12345678".')
                                ->success()
                                ->send();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
