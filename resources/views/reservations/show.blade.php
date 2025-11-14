<div class="container-fluid">
    <h6 class="mb-3">Selecione um horário para reservar:</h6>

    @forelse ($timeSlots as $day => $slots)
        @if (count($slots) > 0)
            <div class="mb-4">
                <h6 class="bg-light p-2 rounded fw-bold mb-2"> {{ ucfirst($day) }}</h6>
                <div class="row row-cols-auto g-2">
                    @foreach ($slots as $slot)
                        @if ($slot['status'] === 'available')
                            <div class="col">
                                <button class="btn btn-sm btn-success btn-block" 
                                        onclick="showReservationForm({{ $table->id }}, {{ $slot['slot_id'] }}, '{{ $slot['time'] }}')">
                                    <strong>{{ $slot['time'] }}</strong><br>
                                    <small>Disponível</small>
                                </button>
                            </div>
                        @else
                            <div class="col">
                                <button class="btn btn-sm btn-danger disabled" title="Reservado por {{ $slot['reservation']->client_name ?? 'Desconhecido' }}">
                                    <strong>{{ $slot['time'] }}</strong><br>
                                    <small>Reservado</small>
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @empty
        <div class="alert alert-warning">Nenhum horário disponível para esta mesa.</div>
    @endforelse
</div>

<!-- Nenhum modal aqui - será incluído no template pai (map.blade.php) -->