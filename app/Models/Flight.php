<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_number',
        'airline_id',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function flightSegments()
    {
        return $this->hasMany(FlightSegment::class);
    }

    public function flightClasses()
    {
        return $this->hasMany(FlightClass::class);
    }

    public function flightSeats()
    {
        return $this->hasMany(FlightSeat::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function generateSeats()
    {
        $classes = $this->flightClasses;
        $rowOffset = 0;

        foreach ($classes as $class) {
            $totalSeats = $class->total_seats;
            $seatsPerRow = $this->getSeatsPerRow($class->class_type);
            $rows = ceil($totalSeats / $seatsPerRow);

            $existingSeats = FlightSeat::where('flight_id', $this->id)
                ->where('class_type', $class->class_type)
                ->get();

            $seatCounter = 1;

            for ($row = 1; $row <= $rows; $row++) {
                for ($column = 1; $column <= $seatsPerRow; $column++) {
                    if ($seatCounter > $totalSeats) {
                        break;
                    }

                    $actualRow = $row + $rowOffset;
                    $seatCode = $this->generateSeatCode($row, $column, $class->class_type);
                    
                    $seatExists = $existingSeats->where('row', $actualRow)
                        ->where('column', $column)
                        ->first();

                    if (!$seatExists) {
                        FlightSeat::create([
                            'flight_id' => $this->id,
                            'name' => $seatCode,
                            'row' => $actualRow,
                            'column' => $column,
                            'is_available' => true,
                            'class_type' => $class->class_type,
                        ]);
                    }

                    $seatCounter++;
                }
            }

            $existingSeats->each(function ($seat) use ($rows, $seatsPerRow, $rowOffset) {
                if ($seat->column > $seatsPerRow || ($seat->row - $rowOffset) > $rows) {
                    $seat->is_available = false;
                    $seat->save();
                }
            });

            $rowOffset += $rows;
        }
    }
    
    protected function getSeatsPerRow($classType)
    {
        switch ($classType) {
            case 'economy':
                return 6;
            case 'business':
                return 4;
            default:
                return 4;
        }
    }
    
    protected function generateSeatCode($row, $column, $classType)
    {
        $prefix = $this->getClassPrefix($classType);
        $rowLetter = chr(64 + $row);
        return $prefix . $rowLetter . $column;
    }

    protected function getClassPrefix($classType)
    {
        switch ($classType) {
            case 'economy':
                return 'E';
            case 'business':
                return 'B';
            default:
                return 'X';
        }
    }
}
