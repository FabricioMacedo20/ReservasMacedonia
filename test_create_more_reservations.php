<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = \Illuminate\Http\Request::capture());

use App\Models\TimeSlot;
use App\Models\Reservation;

// Criar mais reservas na Mesa 1 para teste visual
$slots = TimeSlot::where('table_id', 1)
    ->whereNotIn('id', [1, 2]) // Não sobrescrever as já existentes
    ->take(10)
    ->get();

foreach ($slots as $slot) {
    Reservation::create([
        'table_id' => 1,
        'time_slot_id' => $slot->id,
        'client_name' => 'Cliente Teste ' . uniqid(),
        'client_phone' => '11999999999',
        'client_cpf' => rand(10000000000, 99999999999),
        'status' => 'reserved',
    ]);
}

echo "✓ 10 reservas adicionais criadas na Mesa 1\n";
echo "Ocupação agora: ~24% (12/49)\n";
