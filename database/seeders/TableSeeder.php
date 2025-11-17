<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

// Seeder que cria as 80 mesas e popula os time slots iniciais.
// Executado para gerar dados de teste (mesas + horários).

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $days = ['terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
        $tablesCount = 80;

        for ($i = 1; $i <= $tablesCount; $i++) {
            $table = Table::create([
                'number' => $i,
                'capacity' => rand(2, 10),
                'position_x' => rand(0, 1200),
                'position_y' => rand(0, 500),
            ]);

            // Criar horários para cada dia
            foreach ($days as $day) {
                $times = $this->getTimesForDay($day);
                foreach ($times as $time) {
                    TimeSlot::create([
                        'table_id' => $table->id,
                        'day' => $day,
                        'start_time' => $time,
                        'end_time' => date('H:i', strtotime("$time +1 hour")),
                        'is_available' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Retorna os horários disponíveis para um dia específico
     */
    private function getTimesForDay(string $day): array
    {
        $times = [];

        if ($day === 'sábado') {
            $start = strtotime('11:00');
            $end = strtotime('01:00 +1 day');
        } else {
            $start = strtotime('18:00');
            $end = strtotime('01:00 +1 day');
        }

        while ($start < $end) {
            $times[] = date('H:i', $start);
            $start += 3600;
        }

        return $times;
    }
}