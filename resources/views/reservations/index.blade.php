@extends('layouts.app')

@section('title', 'Minhas Reservas')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1> Minhas Reservas</h1>
        <p class="text-muted">Gerencie suas reservas no restaurante</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if ($reservations->isEmpty())
            <div class="alert alert-info text-center py-5">
                <h4>Nenhuma reserva encontrada</h4>
                <p class="text-muted mb-3">Você ainda não tem reservas. Clique no botão abaixo para fazer uma nova reserva.</p>
                <a href="{{ route('mesas.map') }}" class="btn btn-primary">Ir ao Mapa de Mesas</a>
            </div>
        @else
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mesa</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Dia</th>
                        <th>Horário</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td><strong>#{{ $reservation->table->number }}</strong></td>
                            <td>{{ $reservation->client_name }}</td>
                            <td>{{ $reservation->client_phone }}</td>
                            <td>{{ ucfirst($reservation->timeSlot->day) }}</td>
                            <td>{{ $reservation->timeSlot->start_time }} - {{ $reservation->timeSlot->end_time }}</td>
                            <td>
                                @if ($reservation->status === 'reserved')
                                    <span class="badge bg-success">Reservado</span>
                                @elseif ($reservation->status === 'available')
                                    <span class="badge bg-warning">Disponível</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($reservation->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('reservas.cancel', $reservation) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Cancelar esta reserva?')">
                                        Cancelar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Nenhuma reserva encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection