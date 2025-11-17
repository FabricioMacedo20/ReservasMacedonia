<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo Reservation - representa uma reserva feita por um cliente.
// Conecta mesa e time_slot. Armazena nome, CPF, telefone e status.
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'time_slot_id',
        'client_name',
        'client_phone',
        'client_cpf',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }
}