<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Reserva</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <h1>Criar Reserva</h1>
        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="customer_name">Nome do Cliente</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="table_id">Mesa</label>
                <select class="form-control" id="table_id" name="table_id" required>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">{{ $table->number }} - Capacidade: {{ $table->capacity }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="time_slot_id">Horário</label>
                <select class="form-control" id="time_slot_id" name="time_slot_id" required>
                    @foreach($timeSlots as $timeSlot)
                        <option value="{{ $timeSlot->id }}">{{ $timeSlot->start_time }} - {{ $timeSlot->end_time }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>
    </div>
    @endsection
</body>
</html>