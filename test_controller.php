<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

// Testar diretamente o controller
$container = $app;

// Resolver o TableController
$controller = $container->make('App\Http\Controllers\TableController');

// Obter a table ID 1
$table = \App\Models\Table::find(1);

echo "=== TESTE DO CONTROLLER ===\n";
echo "Table ID: " . $table->id . "\n";
echo "Table Number: " . $table->number . "\n";

// Obter time slots
$days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
$timeSlots = [];

foreach ($days as $day) {
    $slots = \App\Models\TimeSlot::where('table_id', $table->id)
        ->where('day', $day)
        ->orderBy('start_time', 'asc')
        ->get();

    echo "\nDia: $day -> Total de slots: " . $slots->count() . "\n";
    
    $timeSlots[$day] = $slots->map(function ($slot) {
        $reservation = \App\Models\Reservation::where('time_slot_id', $slot->id)
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

echo "\n=== RESUMO ===\n";
echo "Total de dias com horários: " . count($timeSlots) . "\n";
foreach ($timeSlots as $day => $slots) {
    echo "$day: " . count($slots) . " horários\n";
}
