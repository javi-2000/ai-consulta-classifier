<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            LlmConfigSeeder::class,
            PromptVersionSeeder::class,
            CategorySeeder::class,
            ConsultaSeeder::class,
        ]);
    }
}
