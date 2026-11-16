<?php

namespace Tests\Feature;

use App\Enums\ConsultaStatus;
use App\Models\Category;
use App\Models\Consulta;
use App\Models\LlmConfig;
use App\Models\PromptVersion;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);

        LlmConfig::create([
            'provider' => 'openai',
            'model' => 'gpt-4.1-mini',
            'temperature' => 0,
        ]);

        PromptVersion::create([
            'version' => 'v1',
            'prompt' => 'test prompt',
        ]);

        foreach (
            [
                'TECH_SUPPORT',
                'COMMERCIAL',
                'ACKNOWLEDGMENT',
                'OTHER',
                'INVALID'
            ] as $name
        ) {
            Category::create([
                'name' => $name
            ]);
        }
    }

    public function test_home_page_loads_with_title_and_favicon(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ecig Logística', false);
        $response->assertSee('favicon.ico', false);
    }

    public function test_store_rejects_short_messages(): void
    {
        $response = $this->from('/')->post('/consultas', [
            'customer_message' => 'corto',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('customer_message');
        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_store_classifies_and_persists_consulta(): void
    {
        $message = 'Necesito información general sobre vuestros horarios de atención al cliente.';

        $response = $this->from('/')->post('/consultas', [
            'customer_message' => $message,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('consultas', 1);

        $consulta = Consulta::query()->first();

        $this->assertNotNull($consulta);
        $this->assertSame($message, $consulta->customer_message);
        $this->assertSame(ConsultaStatus::Success, $consulta->status);
        $this->assertTrue($consulta->categories->contains('name', 'OTHER'));
    }

    public function test_store_rejects_illegible_text_without_persisting(): void
    {
        $response = $this->from('/')->post('/consultas', [
            'customer_message' => 'asdfgh123456789',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
        $response->assertSessionHasInput('customer_message', 'asdfgh123456789');
        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_home_lists_seeded_consulta_categories(): void
    {
        $consulta = Consulta::create([
            'llm_config_id' => LlmConfig::query()->first()->id,
            'prompt_version_id' => PromptVersion::query()->first()->id,
            'customer_message' => 'Mensaje de prueba para el listado de consultas.',
            'status' => ConsultaStatus::Success,
        ]);

        $consulta->categories()->attach(
            Category::query()->where('name', 'TECH_SUPPORT')->value('id')
        );

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Mensaje de prueba para el listado de consultas.', false);
        $response->assertSee('TECH_SUPPORT', false);
        $response->assertSee('SUCCESS', false);
    }

    public function test_favicon_file_exists_in_public(): void
    {
        $this->assertFileExists(public_path('favicon.ico'));
    }
}
