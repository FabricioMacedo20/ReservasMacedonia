<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Reservation;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * TableController
 * Controller responsável por operações relacionadas a mesas e seus horários.
 * Métodos principais: map(), show(), index(), available().
 */
class TableController extends Controller
{
    // map() - retorna view do mapa com cálculo simples de ocupação por mesa
    public function map(): View
    {
        // Mapear cada mesa com cálculos de ocupação
        $tables = Table::all()->sortBy('number')->map(function ($table) {
            // Calcula total de horários = 6 dias × 49-52 horários/dia
            $totalSlots = TimeSlot::where('table_id', $table->id)->count();
            
            // Conta quantos horários foram RESERVADOS (status = 'reserved')
            $reservedSlots = Reservation::where('table_id', $table->id)
                ->where('status', 'reserved')
                ->count();
            
            // Calcula percentual para a barra de progresso (0-100%)
            $occupancyPercent = $totalSlots > 0 ? round(($reservedSlots / $totalSlots) * 100) : 0;
            
            // Atribui dados dinamicamente à mesa para uso na view
            $table->occupancy_percent = $occupancyPercent;
            $table->reserved_slots = $reservedSlots;
            $table->total_slots = $totalSlots;
            
            return $table;
        });
        
        return view('reservations.map', compact('tables'));
    }

    // show($id) - retorna HTML com os horários de uma mesa (usado por AJAX)
    public function show($id): View
    {
        // Busca a mesa pelo ID ou retorna 404
        $table = Table::find($id);
        
        if (!$table) {
            abort(404, 'Mesa não encontrada');
        }

        // Array de dias da semana em português
        $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
        $timeSlots = [];

        // Itera cada dia da semana
        foreach ($days as $day) {
            // Busca todos os horários para essa mesa e esse dia
            $slots = TimeSlot::where('table_id', $table->id)
                ->where('day', $day)
                ->orderBy('start_time', 'asc')
                ->get();

            if ($slots->count() > 0) {
                // Para cada horário, verifica se tem reserva
                $timeSlots[$day] = $slots->map(function ($slot) {
                    // Busca se existe reserva ATIVA para esse time slot
                    $reservation = Reservation::where('time_slot_id', $slot->id)
                        ->where('status', 'reserved')
                        ->first();

                    // Retorna array com dados do horário
                    return [
                        'slot_id' => $slot->id,
                        'time' => $slot->start_time,
                        'status' => $reservation ? 'reserved' : 'available',
                        'reservation' => $reservation,
                    ];
                })->toArray();
            }
        }

        return view('reservations.show', compact('table', 'timeSlots'));
    }

    // index() - lista mesas (pode ser usado para administração)
    public function index(): View
    {
        $tables = Table::all();
        return view('reservations.index', compact('tables'));
    }

    // available() - retorna JSON com mesas e time slots (API)
    public function available(): JsonResponse
    {
        // Eager loading: carrega time slots de uma vez (otimiza query)
        $tables = Table::with('timeSlots')->get();
        return response()->json($tables);
    }
}