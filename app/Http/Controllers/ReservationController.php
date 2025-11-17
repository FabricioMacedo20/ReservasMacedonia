<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Services\ReservationService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * ReservationController - Gerencia operações de reservas
 * 
 * Responsabilidades:
 * - Listar todas as reservas ativas
 * - Criar novas reservas
 * - Visualizar detalhes de uma reserva
 * - Cancelar reservas
 * 
 * Utiliza ReservationService para encapsular lógica de negócio
 */
class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    // Injeção de dependência do serviço
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * index() - Lista todas as reservas ativas
     * 
     * Filtra apenas reservas com status 'reserved'
     * Carrega relacionamentos (mesa e time slot) com eager loading
     * 
     * @return View - Exibe tabela com histórico de reservas
     */
    public function index(): View
    {
        // Eager load: carrega mesas e time slots em uma única query
        $reservations = Reservation::where('status', 'reserved')
            ->with('table', 'timeSlot')
            ->get();
        
        return view('reservations.index', compact('reservations'));
    }

    /**
     * create() - Exibe formulário de criação de reserva (não utilizado no fluxo atual)
     * 
     * O sistema usa modal ao invés disso
     * 
     * @return View
     */
    public function create(): View
    {
        $tables = Table::all();
        return view('reservations.create', compact('tables'));
    }

    /**
     * store() - Cria uma nova reserva
     * 
     * Fluxo:
     * 1. Valida dados do formulário (table_id, time_slot_id, nome, CPF, telefone)
     * 2. Valida que tabela e time_slot existem no banco
     * 3. Valida que CPF tem exatamente 11 dígitos
     * 4. Passa dados para ReservationService
     * 5. Redireciona para listagem com mensagem de sucesso
     * 
     * @param Request $request - Dados do formulário
     * @return RedirectResponse - Redireciona para /reservas
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida todos os campos obrigatórios
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',           // Verifica se mesa existe
            'time_slot_id' => 'required|exists:time_slots,id',   // Verifica se horário existe
            'client_name' => 'required|string|max:255',           // Nome do cliente
            'client_phone' => 'required|string|max:20',           // Telefone
            'client_cpf' => 'required|digits:11',                 // CPF com exatamente 11 dígitos
        ]);

        // Delega a lógica de criação para o serviço
        $this->reservationService->createReservation($validated);

        // Redireciona com mensagem de sucesso
        return redirect()->route('reservas.index')->with('success', 'Reserva criada com sucesso!');
    }

    /**
     * show() - Exibe detalhes de uma reserva específica
     * 
     * @param Reservation $reservation - Modelo binding automático
     * @return View
     */
    public function show(Reservation $reservation): View
    {
        return view('reservations.show', compact('reservation'));
    }

    /**
     * cancel() - Cancela uma reserva
     * 
     * Marca a reserva como 'available' (disponível novamente)
     * Libera o horário para outras reservas
     * 
     * @param Reservation $reservation - Modelo binding automático
     * @return RedirectResponse - Redireciona para /reservas
     */
    public function cancel(Reservation $reservation): RedirectResponse
    {
        // Utiliza serviço para cancelar
        $this->reservationService->cancelReservation($reservation);
        
        return redirect()->back()->with('success', 'Reserva cancelada com sucesso!');
    }
}