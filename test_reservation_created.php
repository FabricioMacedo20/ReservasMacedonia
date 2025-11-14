<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = \Illuminate\Http\Request::capture());

use App\Models\Reservation;

$latest = Reservation::latest()->first();
if ($latest) {
    echo "✓ Reserva criada com sucesso!\n";
    echo "ID: " . $latest->id . "\n";
    echo "Mesa: " . $latest->table_id . "\n";
    echo "Nome: " . $latest->client_name . "\n";
    echo "CPF: " . $latest->client_cpf . "\n";
    echo "Telefone: " . $latest->client_phone . "\n";
    echo "Status: " . $latest->status . "\n";
    echo "Criado em: " . $latest->created_at . "\n";
} else {
    echo "✗ Nenhuma reserva encontrada\n";
}
