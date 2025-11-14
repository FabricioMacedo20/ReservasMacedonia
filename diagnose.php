<?php
$pdo = new PDO('sqlite:database/database.sqlite');

echo "=== DIAGNÓSTICO DO BANCO DE DADOS ===\n\n";

// Contar mesas
$result = $pdo->query('SELECT COUNT(*) as count FROM tables');
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "Total de Mesas: " . $count['count'] . "\n";

// Primeiras 3 mesas
echo "\nPrimeiras 3 Mesas:\n";
$result = $pdo->query('SELECT * FROM tables LIMIT 3');
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row) {
    echo "ID: {$row['id']}, Número: {$row['number']}, PosX: {$row['position_x']}, PosY: {$row['position_y']}\n";
}

// Contar time slots
$result = $pdo->query('SELECT COUNT(*) as count FROM time_slots');
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "\nTotal de Time Slots: " . $count['count'] . "\n";

// Contar reservations
$result = $pdo->query('SELECT COUNT(*) as count FROM reservations');
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "Total de Reservations: " . $count['count'] . "\n";

// Time slots da primeira mesa
echo "\nTime Slots da Mesa 1:\n";
$result = $pdo->query('SELECT day, start_time, end_time FROM time_slots WHERE table_id = 1 LIMIT 5');
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row) {
    echo "{$row['day']} {$row['start_time']}-{$row['end_time']}\n";
}
