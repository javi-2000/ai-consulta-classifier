<?php

namespace Database\Seeders;

use App\Enums\ConsultaStatus;
use App\Models\Category;
use App\Models\Consulta;
use App\Models\LlmConfig;
use App\Models\PromptVersion;
use Illuminate\Database\Seeder;


class ConsultaSeeder extends Seeder
{
    public function run(): void
    {
        $openai = LlmConfig::query()
            ->where('provider', 'openai')
            ->where('model', 'gpt-4.1-mini')
            ->firstOrFail();

        $anthropic = LlmConfig::query()
            ->where('provider', 'anthropic')
            ->where('model', 'claude-sonnet')
            ->firstOrFail();

        $promptV1 = PromptVersion::query()
            ->where('version', 'v1')
            ->firstOrFail();

        $promptV2 = PromptVersion::query()
            ->where('version', 'v2')
            ->firstOrFail();

        $techSupportId = Category::query()
            ->where('name', 'TECH_SUPPORT')
            ->value('id');

        $commercialId = Category::query()
            ->where('name', 'COMMERCIAL')
            ->value('id');

        $acknowledgmentId = Category::query()
            ->where('name', 'ACKNOWLEDGMENT')
            ->value('id');

        $consulta = Consulta::create([
            'llm_config_id' => $openai->id,
            'prompt_version_id' => $promptV1->id,
            'customer_message' => 'Estoy intentando localizar mi paquete pero en el email que me has dado no aparece el ID para hacer el seguimiento',
            'status' => ConsultaStatus::Success,
        ]);

        $consulta->categories()->attach([
            $techSupportId,
        ]);


        // Consulta 2 - ACKNOWLEDGMENT

        $consulta = Consulta::create([
            'llm_config_id' => $openai->id,
            'prompt_version_id' => $promptV2->id,
            'customer_message' => 'Muchas gracias por vuestra atención, un saludo',
            'status' => ConsultaStatus::Success,
        ]);

        $consulta->categories()->attach([
            $acknowledgmentId,
        ]);


        // Consulta 3 - MULTI CATEGORY
        // Ejemplo: soporte + comercial

        $consulta = Consulta::create([
            'llm_config_id' => $anthropic->id,
            'prompt_version_id' => $promptV2->id,
            'customer_message' => 'Incidencia con mi pedido. En el email de confirmación de compra indicaba que llegaba ayer pero aún no lo he recibido. Por otra parte, he visto que tenéis nuevos descuentos para próximos pedidos y me interesaría saber si son aplicables al pedido que ya he realizado.',
            'status' => ConsultaStatus::Success,
        ]);

        $consulta->categories()->attach([
            $techSupportId,
            $commercialId,
        ]);

    }
}
