<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::create('/mesas/1', 'GET')
);

$content = $response->getContent();

echo "=== TESTE DE ROTA /mesas/1 ===\n";
echo "Status: " . $response->getStatusCode() . "\n";
echo "Total de caracteres: " . strlen($content) . "\n";

// Verifica se a tabela/conteúdo de horários está presente
if (strpos($content, '<div class="mb-4">') !== false) {
    echo "✓ Conteúdo de horários encontrado\n";
} else {
    echo "✗ Conteúdo de horários NÃO encontrado\n";
}

// Verifica se o modal está presente
if (strpos($content, 'reservationModal') !== false) {
    echo "✓ Modal de reserva encontrado\n";
} else {
    echo "✗ Modal de reserva NÃO encontrado\n";
}

// Verifica se há mensagem de forelse
if (strpos($content, 'Nenhum horário disponível') !== false) {
    echo "⚠ ALERTA: Mensagem 'Nenhum horário disponível' encontrada\n";
} else {
    echo "✓ Horários foram carregados\n";
}

echo "\n=== SAÍDA COMPLETA (últimos 1000 caracteres) ===\n";
echo substr($content, -1000) . "\n";
