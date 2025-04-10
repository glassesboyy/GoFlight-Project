<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
USE Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image',
        'name',
        'description',
    ];
}
