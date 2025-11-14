<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use Carbon\Carbon;

class ReservationService
{
    /**
     * Obtém os horários para uma mesa específica
     */
    public function getTimeSlotsForTable(Table $table): array
    {
        $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
        $timeSlots = [];

        foreach ($days as $day) {
            $times = $this->getTimesForDay($day);
            $timeSlots[$day] = [];

            foreach ($times as $time) {
                $slot = TimeSlot::where('table_id', $table->id)
                    ->where('day', $day)
                    ->where('start_time', $time)
                    ->first();

                if ($slot) {
                    $reservation = Reservation::where('time_slot_id', $slot->id)
                        ->where('status', 'reserved')
                        ->first();

                    $timeSlots[$day][] = [
                        'time' => $time,
                        'slot_id' => $slot->id,
                        'status' => $reservation ? 'reserved' : 'available',
                        'reservation' => $reservation,
                    ];
                }
            }
        }

        return $timeSlots;
    }

    /**
     * Obtém os horários disponíveis para um dia específico
     */
    public function getTimesForDay(string $day): array
    {
        $times = [];

        if ($day === 'sábado') {
            $start = strtotime('11:00');
            $end = strtotime('01:00 +1 day');
        } else {
            $start = strtotime('18:00');
            $end = strtotime('01:00 +1 day');
        }

        while ($start < $end) {
            $times[] = date('H:i', $start);
            $start += 3600;
        }

        return $times;
    }

    /**
     * Cria uma nova reserva
     */
    public function createReservation(array $data): Reservation
    {
        return Reservation::create([
            'table_id' => $data['table_id'],
            'time_slot_id' => $data['time_slot_id'],
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_cpf' => $data['client_cpf'] ?? null,
            'status' => 'reserved',
        ]);
    }

    /**
     * Cancela uma reserva
     */
    public function cancelReservation(Reservation $reservation): void
    {
        $reservation->update(['status' => 'available']);
    }

    /**
     * Marca reservas expiradas como disponíveis
     */
    public function expireReservations(): void
    {
        $now = Carbon::now();
        
        $expiredReservations = Reservation::join('time_slots', 'reservations.time_slot_id', '=', 'time_slots.id')
            ->where('reservations.status', 'reserved')
            ->whereRaw("TIME(CONCAT(?, ' ', time_slots.end_time)) < TIME(?)", [
                $now->format('Y-m-d'),
                $now->format('H:i:s')
            ])
            ->select('reservations.*')
            ->get();

        foreach ($expiredReservations as $reservation) {
            $reservation->update(['status' => 'expired']);
        }
    }
}
