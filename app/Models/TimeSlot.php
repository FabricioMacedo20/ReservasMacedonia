<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo TimeSlot - representa um horário disponível para uma mesa.
// Armazena dia, hora início/fim, disponibilidade. Criado pelo seeder.
class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'day',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}