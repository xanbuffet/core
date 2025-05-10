<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('day_of_week')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\CheckboxList::make('dishes')
                    ->label('Chọn món ăn')
                    ->relationship('dishes', 'name')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 2,
                        'md' => 3,
                        'lg' => 4,
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên thực đơn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Ngày trong tuần')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dishes.name')
                    ->label('Món ăn')
                    ->limit(3)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Thực đơn hôm nay')
                    ->query(fn (Builder $query): Builder => $query->where('day_of_week', now()->format('l')))
                    ->indicateUsing(fn (array $data): array => [
                        'today' => 'Thực đơn hôm nay',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageMenus::route('/'),
        ];
    }
}
