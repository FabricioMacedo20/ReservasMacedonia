@extends('layouts.app')

@section('title', 'Mapa de Mesas')

@section('styles')
<style>
    .map-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border: 3px solid #333;
        border-radius: 8px;
        padding: 20px;
        height: 700px;
        overflow: auto;
        position: relative;
        display: grid;
        grid-template-columns: repeat(auto-fill, 100px);
        gap: 20px;
        align-content: start;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .table-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 3px solid;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .table-circle .table-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 100%;
        background-color: rgba(220, 53, 69, 0.6);
        transition: width 0.4s ease;
        border-radius: 50%;
        z-index: 1;
    }

    .table-circle .table-number {
        position: relative;
        z-index: 2;
        font-size: 20px;
        font-weight: bold;
    }

    .table-circle.available {
        background-color: #28a745;
        border-color: #1e7e34;
    }

    .table-circle.available:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
    }

    .table-circle.reserved {
        background-color: #dc3545;
        border-color: #bd2130;
    }

    .table-circle.reserved:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
    }

    .table-circle:active {
        transform: scale(0.95);
    }

    .legend-box {
        margin-top: 30px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .legend-item {
        display: inline-block;
        margin-right: 40px;
        font-size: 16px;
    }

    .legend-color {
        display: inline-block;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
        vertical-align: middle;
        border: 2px solid #333;
    }

    .header-info {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-info">
            <h1>Mapa de Mesas do Restaurante</h1>
            <p class="text-muted mb-0">Total de Mesas: <strong>{{ $tables->count() }}</strong> | Clique em qualquer mesa para ver horários disponíveis</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="map-container" id="mapContainer">
            @foreach ($tables as $table)
                <div class="table-circle available" 
                     data-table-id="{{ $table->id }}"
                     data-table-number="{{ $table->number }}"
                     onclick="openTableModal({{ $table->id }}, {{ $table->number }})"
                     title="Mesa {{ $table->number }}&#10;Ocupação: {{ $table->occupancy_percent }}%&#10;{{ $table->reserved_slots }}/{{ $table->total_slots }} horários reservados">
                    <div class="table-progress" style="width: {{ $table->occupancy_percent }}%;"></div>
                    <div class="table-number">{{ $table->number }}</div>
                </div>
            @endforeach
        </div>

        <div class="legend-box">
            <div class="legend-item">
                <span class="legend-color" style="background-color: #28a745;"></span>
                <span><strong>Verde</strong> = Disponível</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #dc3545;"></span>
                <span><strong>Vermelho</strong> = Reservado</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Horários -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">📅 Horários - Mesa <strong id="modalTableNumber"></strong></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reserva (fora do scheduleModal para evitar aninhamento) -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">💰 Fazer Reserva - Mesa <span id="reservationTableNumber"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="reservationForm" method="POST" action="{{ route('reservas.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="table_id" id="formTableId">
                    <input type="hidden" name="time_slot_id" id="formTimeSlotId">
                    
                    <div class="alert alert-info" id="selectedTimeAlert">
                        Horário: <strong id="selectedTime"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clientName" class="form-label">👤 Nome do Cliente</label>
                        <input type="text" class="form-control" id="clientName" name="client_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clientCpf" class="form-label">🆔 CPF (somente números)</label>
                        <input type="text" class="form-control" id="clientCpf" name="client_cpf" placeholder="Somente números, ex: 12345678909" pattern="\d{11}" required>
                    </div>

                    <div class="mb-3">
                        <label for="clientPhone" class="form-label">📱 Telefone</label>
                        <input type="tel" class="form-control" id="clientPhone" name="client_phone" placeholder="(XX) XXXXX-XXXX" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">✓ Confirmar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTableModal(tableId, tableNumber) {
        document.getElementById('modalTableNumber').textContent = tableNumber;
        document.getElementById('modalContent').innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';
        
        const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        modal.show();
        
        console.log('[debug] openTableModal chamado para mesaId=', tableId, ' número=', tableNumber);
        // Carregar conteúdo da mesa
        fetch(`/mesas/${tableId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                console.log('[debug] HTML recebido para mesa', tableId, 'tamanho:', html.length);
                document.getElementById('modalContent').innerHTML = html;
                // log do conteúdo injetado (trim para evitar excesso)
                try { console.log('[debug] Conteúdo injetado (preview):', document.getElementById('modalContent').innerHTML.slice(0, 800)); } catch(e){}
            })
            .catch(error => {
                console.error('Erro ao carregar horários:', error);
                document.getElementById('modalContent').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <strong>Erro!</strong> Não foi possível carregar os horários. 
                        <small class="d-block">Detalhes: ${error.message}</small>
                    </div>
                `;
            });
    }
    
    // Função global para abrir o formulário de reserva (chamada pelos botões injetados)
    function showReservationForm(tableId, timeSlotId, time) {
        console.log('[debug] showReservationForm chamado', {tableId, timeSlotId, time});
        
        // Preencher os campos do modal de reserva
        document.getElementById('formTableId').value = tableId;
        document.getElementById('formTimeSlotId').value = timeSlotId;
        document.getElementById('selectedTime').textContent = time;
        document.getElementById('reservationTableNumber').textContent = tableId;
        document.getElementById('clientName').value = '';
        document.getElementById('clientPhone').value = '';
        document.getElementById('clientCpf').value = '';

        // Fechar o modal de horários
        const scheduleModalInstance = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
        if (scheduleModalInstance) {
            scheduleModalInstance.hide();
        }
        
        // Abrir o modal de reserva
        const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
        reservationModal.show();
        console.log('[debug] Modal de reserva aberto com sucesso');
    }

    // Função global para cancelar uma reserva (chamada pelos botões injetados)
    function cancelReservation(reservationId) {
        if (!confirm('Tem certeza que deseja cancelar esta reserva?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/reservas/${reservationId}/cancelar`;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = token;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection