<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::create('/mesas/1', 'GET')
);

echo "=== TESTE DE ROTA /mesas/1 ===\n";
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
echo "Content Length: " . strlen($response->getContent()) . "\n";
echo "\n=== PRIMEIROS 500 CARACTERES ===\n";
echo substr($response->getContent(), 0, 500) . "\n";
