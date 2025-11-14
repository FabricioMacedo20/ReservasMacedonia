<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

// Resolver diretamente via container (já inicializado)
\Illuminate\Support\Facades\DB::setFacadeApplication($app);

$table = \App\Models\Table::find(1);

echo "=== TESTE DO BANCO ===\n";
if ($table) {
    echo "✓ Table encontrada: " . $table->number . "\n";
    
    $count = \App\Models\TimeSlot::where('table_id', 1)->count();
    echo "Total de TimeSlots para Mesa 1: " . $count . "\n";
    
    // Contar por dia
    $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
    foreach ($days as $day) {
        $dayCount = \App\Models\TimeSlot::where('table_id', 1)
            ->where('day', $day)
            ->count();
        echo "  $day: $dayCount\n";
    }
} else {
    echo "✗ Table não encontrada\n";
}
