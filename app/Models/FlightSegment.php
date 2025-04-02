<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FlightSegment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sequence',
        'flight_id',
        'airport_id',
        'time',
    ];
}
