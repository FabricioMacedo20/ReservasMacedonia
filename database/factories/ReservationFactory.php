<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'table_id' => $this->faker->numberBetween(1, 80),
            'time_slot_id' => $this->faker->numberBetween(1, 10), // Supondo que haja 10 slots de tempo
            'customer_name' => $this->faker->name,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}