<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Flight Information')
                    ->schema([
                        Forms\Components\Select::make('flight_id')
                            ->relationship('flight', 'flight_number')
                            ->required()
                            ->label('Flight'),
                        Forms\Components\Select::make('flight_class_id')
                            ->relationship('flightClass', 'class_type')
                            ->required()
                            ->label('Flight Class'),
                    ]),
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Contact Person Name'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->label('Contact Email'),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->tel()
                            ->label('Contact Phone Number'),
                    ]),
                Forms\Components\Section::make('Passenger Details')
                    ->schema([
                        Forms\Components\TextInput::make('number_of_passengers')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->label('Number of Passengers'),
                        Forms\Components\Repeater::make('transactionPassengers')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('flight_seat_id')
                                    ->relationship('flightSeat', 'name')
                                    ->required()
                                    ->label('Seat Number'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Passenger Name'),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->required()
                                    ->label('Date of Birth'),
                                Forms\Components\TextInput::make('nationality')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nationality'),
                            ])
                            ->columns(2)
                    ]),
                Forms\Components\Section::make('Promo & Payment')
                    ->schema([
                        Forms\Components\TextInput::make('promo_code')
                            ->label('Promo Code'),
                        Forms\Components\TextInput::make('subtotal')
                            ->disabled()
                            ->label('Subtotal')
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('grandtotal')
                            ->disabled()
                            ->label('Grand Total')
                            ->prefix('IDR'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Transaction Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('flight.code')
                    ->label('Flight Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('flightClass.name')
                    ->label('Flight Class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Customer Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_passengers')
                    ->label('Total Passengers')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('promoCode.code')
                    ->label('Promo Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grandtotal')
                    ->label('Grand Total')
                    ->prefix('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
