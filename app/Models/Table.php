<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'capacity',
        'position_x',
        'position_y',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}