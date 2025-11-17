<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use Carbon\Carbon;

/**
 * ReservationService
 * Serviço que centraliza a lógica de reservas (validação, criação, cancelamento).
 * Mantém os controllers enxutos e facilita testes.
 */
class ReservationService
{
    // getTimeSlotsForTable(Table) - retorna horários por dia com status e possível reserva
    public function getTimeSlotsForTable(Table $table): array
    {
        // Dias da semana em português
        $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
        $timeSlots = [];

        // Para cada dia, obtém os horários
        foreach ($days as $day) {
            $times = $this->getTimesForDay($day);
            $timeSlots[$day] = [];

            foreach ($times as $time) {
                // Busca o time slot específico para essa mesa, dia e hora
                $slot = TimeSlot::where('table_id', $table->id)
                    ->where('day', $day)
                    ->where('start_time', $time)
                    ->first();

                if ($slot) {
                    // Verifica se há reserva ativa para esse horário
                    $reservation = Reservation::where('time_slot_id', $slot->id)
                        ->where('status', 'reserved')
                        ->first();

                    // Adiciona ao array de horários
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

    // getTimesForDay(day) - gera lista de horas para o dia informado
    public function getTimesForDay(string $day): array
    {
        $times = [];

        // Sábado abre mais cedo (11:00)
        if ($day === 'sábado') {
            $start = strtotime('11:00');
            $end = strtotime('01:00 +1 day');
        } else {
            // Outros dias: 18:00 - 01:00
            $start = strtotime('18:00');
            $end = strtotime('01:00 +1 day');
        }

        // Gera horários a cada 1 hora (3600 segundos)
        while ($start < $end) {
            $times[] = date('H:i', $start);
            $start += 3600;
        }

        return $times;
    }

    // createReservation(array) - cria uma reserva e retorna o modelo criado
    public function createReservation(array $data): Reservation
    {
        return Reservation::create([
            'table_id' => $data['table_id'],
            'time_slot_id' => $data['time_slot_id'],
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_cpf' => $data['client_cpf'] ?? null,
            'status' => 'reserved',  // Marca como ativa
        ]);
    }

    // cancelReservation(Reservation) - marca reserva como disponível
    public function cancelReservation(Reservation $reservation): void
    {
        $reservation->update(['status' => 'available']);
    }

    // expireReservations() - procura reservas cuja hora já passou e marca como 'expired'
    public function expireReservations(): void
    {
        $now = Carbon::now();
        
        // Busca reservas ativas cujo horário final já passou
        $expiredReservations = Reservation::join('time_slots', 'reservations.time_slot_id', '=', 'time_slots.id')
            ->where('reservations.status', 'reserved')
            ->whereRaw("TIME(CONCAT(?, ' ', time_slots.end_time)) < TIME(?)", [
                $now->format('Y-m-d'),
                $now->format('H:i:s')
            ])
            ->select('reservations.*')
            ->get();

        // Marca cada reserva vencida como expirada
        foreach ($expiredReservations as $reservation) {
            $reservation->update(['status' => 'expired']);
        }
    }
}