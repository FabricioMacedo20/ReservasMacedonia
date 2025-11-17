<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

// Factory para criar dados de mesa (used em seeders e testes).
// Gera número e capacidade para cada mesa.

class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition()
    {
        return [
            'number' => $this->faker->unique()->numberBetween(1, 80),
            'capacity' => $this->faker->numberBetween(2, 10),
        ];
    }
}