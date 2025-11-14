<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::create('/mesas/1', 'GET')
);

$content = $response->getContent();

// Salvar em arquivo para análise
file_put_contents('debug_output.html', $content);

// Verificar estrutura
echo "=== ANÁLISE DA RESPOSTA ===\n";
echo "Total de caracteres: " . strlen($content) . "\n";

// Procura por seções principais
$sections = [
    'container-fluid' => 'Div container com horários',
    'mb-4' => 'Blocos de dias',
    'btn-success' => 'Botões verdes (disponíveis)',
    'btn-danger' => 'Botões vermelhos (reservados)',
    'forelse' => 'Estrutura forelse',
    'reservationModal' => 'Modal de reserva',
];

foreach ($sections as $search => $desc) {
    if (strpos($content, $search) !== false) {
        echo "✓ Encontrado: $desc\n";
    } else {
        echo "✗ NÃO ENCONTRADO: $desc\n";
    }
}

echo "\nArquivo salvo em: debug_output.html\n";
