<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

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