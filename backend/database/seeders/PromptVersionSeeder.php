<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromptVersion;


class PromptVersionSeeder extends Seeder
{
    public function run(): void
    {
        PromptVersion::create([
            'version' => 'v1',
            'prompt' => file_get_contents(
                resource_path('prompts/prompt_version1.txt')
            )
        ]);

        PromptVersion::create([
            'version' => 'v2',
            'prompt' => file_get_contents(
                resource_path('prompts/prompt_version2.txt')
            )
        ]);
    }
}