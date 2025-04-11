<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iata_code',
        'name',
        'image',
        'city',
        'country',
    ];

    public function flightSegments()
    {
        return $this->hasMany(FlightSegment::class);
    }
}
