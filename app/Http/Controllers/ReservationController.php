<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Services\ReservationService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Lista todas as reservas
     */
    public function index(): View
    {
        $reservations = Reservation::where('status', 'reserved')
            ->with('table', 'timeSlot')
            ->get();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Exibe o formulário de criação
     */
    public function create(): View
    {
        $tables = Table::all();
        return view('reservations.create', compact('tables'));
    }

    /**
     * Armazena uma nova reserva
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_cpf' => 'required|digits:11',
        ]);

        $this->reservationService->createReservation($validated);

        return redirect()->route('reservas.index')->with('success', 'Reserva criada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma reserva
     */
    public function show(Reservation $reservation): View
    {
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Cancela uma reserva
     */
    public function cancel(Reservation $reservation): RedirectResponse
    {
        $this->reservationService->cancelReservation($reservation);
        return redirect()->back()->with('success', 'Reserva cancelada com sucesso!');
    }
}