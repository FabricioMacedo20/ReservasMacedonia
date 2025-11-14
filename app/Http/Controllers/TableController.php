<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Reservation;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    /**
     * Exibe o mapa de mesas com percentual de ocupação
     */
    public function map(): View
    {
        $tables = Table::all()->sortBy('number')->map(function ($table) {
            // Calcular total de horários disponíveis
            $totalSlots = TimeSlot::where('table_id', $table->id)->count();
            
            // Calcular horários reservados
            $reservedSlots = Reservation::where('table_id', $table->id)
                ->where('status', 'reserved')
                ->count();
            
            // Percentual de ocupação
            $occupancyPercent = $totalSlots > 0 ? round(($reservedSlots / $totalSlots) * 100) : 0;
            
            $table->occupancy_percent = $occupancyPercent;
            $table->reserved_slots = $reservedSlots;
            $table->total_slots = $totalSlots;
            
            return $table;
        });
        
        return view('reservations.map', compact('tables'));
    }

    /**
     * Retorna os horários de uma mesa em HTML - Versão com ID
     */
    public function show($id): View
    {
        // Buscar a mesa pelo ID
        $table = Table::find($id);
        
        if (!$table) {
            abort(404, 'Mesa não encontrada');
        }

        $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
        $timeSlots = [];

        foreach ($days as $day) {
            $slots = TimeSlot::where('table_id', $table->id)
                ->where('day', $day)
                ->orderBy('start_time', 'asc')
                ->get();

            if ($slots->count() > 0) {
                $timeSlots[$day] = $slots->map(function ($slot) {
                    $reservation = Reservation::where('time_slot_id', $slot->id)
                        ->where('status', 'reserved')
                        ->first();

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

    /**
     * Lista todas as mesas
     */
    public function index(): View
    {
        $tables = Table::all();
        return view('reservations.index', compact('tables'));
    }

    /**
     * Retorna mesas disponíveis em JSON
     */
    public function available(): JsonResponse
    {
        $tables = Table::with('timeSlots')->get();
        return response()->json($tables);
    }
}