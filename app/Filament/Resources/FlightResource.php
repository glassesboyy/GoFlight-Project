<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Filament\Resources\FlightResource\RelationManagers;
use App\Models\Flight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Flight Information')
                        ->schema([
                            Forms\Components\TextInput::make('flight_number')
                                ->label('Flight Number')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Forms\Components\Select::make('airline_id')
                                ->label('Airline Name')
                                ->relationship('airline', 'name')
                                ->required()
                                ->preload()
                                ->searchable(),
                        ]),
                    Forms\Components\Wizard\Step::make('Flight Segments')
                        ->schema([
                            Forms\Components\Repeater::make('segments')
                                ->relationship('flightSegments')
                                ->schema([
                                    Forms\Components\TextInput::make('sequence')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\Select::make('airport_id')
                                        ->label('Airport Name')
                                        ->relationship('airport', 'name')
                                        ->required()
                                        ->preload()
                                        ->searchable(),
                                    Forms\Components\DateTimePicker::make('time')
                                        ->required()
                                        ->native(false),
                                ])
                                ->orderColumn('sequence')
                                ->minItems(1)
                                ->columns(3),
                        ]),
                    Forms\Components\Wizard\Step::make('Flight Class')
                        ->schema([
                            Forms\Components\Repeater::make('flightClasses')
                                ->relationship('flightClasses')
                                ->schema([
                                    Forms\Components\Select::make('class_type')
                                        ->label('Class Type')
                                        ->options([
                                            'economy' => 'Economy',
                                            'business' => 'Business',
                                        ])
                                        ->required(),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Price')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->prefix('Rp'),
                                    Forms\Components\TextInput::make('total_seats')
                                        ->label('Total Seats')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1),
                                    Forms\Components\Select::make('facilities')
                                        ->multiple()
                                        ->relationship('facilities', 'name')
                                        ->preload()
                                        ->searchable(),
                                ])
                                ->columns(4)
                                ->minItems(1),
                        ]),
                ])->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
