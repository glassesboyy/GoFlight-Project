<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Filament\Resources\PromoCodeResource\RelationManagers;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Promo Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('discount_type')
                    ->label('Discount Type')
                    ->required()
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Forms\Components\TextInput::make('discount')
                    ->label('Discount Amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix(function ($get) {
                        return $get('discount_type') === 'percentage' ? '%' : 'Rp';
                    })
                    ->live(),
                Forms\Components\DateTimePicker::make('valid_until')
                    ->label('Valid Until')
                    ->required()
                    ->native(false),
                Forms\Components\Toggle::make('is_used')
                    ->label('Is Used')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Promo Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Discount Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label('Discount Amount')
                    ->formatStateUsing(fn ($record) =>
                        $record->discount_type === 'percentage'
                            ? $record->discount . '%'
                            : 'Rp ' . number_format($record->discount, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Valid Until')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_used')
                    ->label('Is Used'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->default(now())
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->default(now())
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Tables\Filters\Filter::make('valid')
                    ->query(fn (Builder $query) => $query->where('valid_until', '>=', now())),
                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query) => $query->where('valid_until', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
