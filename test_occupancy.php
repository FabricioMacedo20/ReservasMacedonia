<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = \Illuminate\Http\Request::capture());

use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Reservation;

// Verificar ocupação de algumas mesas
$tables = Table::whereIn('id', [1, 2, 3, 5, 10])->get();

echo "\n=== RELATÓRIO DE OCUPAÇÃO DAS MESAS ===\n\n";

foreach ($tables as $table) {
    $totalSlots = TimeSlot::where('table_id', $table->id)->count();
    $reservedSlots = Reservation::where('table_id', $table->id)
        ->where('status', 'reserved')
        ->count();
    $occupancyPercent = $totalSlots > 0 ? round(($reservedSlots / $totalSlots) * 100) : 0;
    
    $bar = str_repeat('█', intval($occupancyPercent / 10)) . str_repeat('░', 10 - intval($occupancyPercent / 10));
    
    echo "Mesa {$table->number}: [$bar] {$occupancyPercent}% ({$reservedSlots}/{$totalSlots})\n";
}

echo "\n✓ Teste concluído!\n";
