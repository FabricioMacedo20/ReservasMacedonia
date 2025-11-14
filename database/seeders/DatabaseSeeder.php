<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TableSeeder::class);
        // Aqui você pode adicionar outros seeders conforme necessário
    }
}