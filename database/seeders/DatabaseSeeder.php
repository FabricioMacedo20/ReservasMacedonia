<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

// Seeder principal que chama outros seeders (ex.: TableSeeder).
// Use para popular o banco com dados iniciais para desenvolvimento.

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