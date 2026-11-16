<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LlmConfig;


class LlmConfigSeeder extends Seeder
{
    public function run(): void
    {
        LlmConfig::create([
            'provider' => 'openai',
            'model' => 'gpt-4.1-mini',
            'temperature' => 0
        ]);


        LlmConfig::create([
            'provider' => 'anthropic',
            'model' => 'claude-sonnet',
            'temperature' => 0
        ]);
    }
}