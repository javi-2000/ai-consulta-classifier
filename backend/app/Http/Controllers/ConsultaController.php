<?php

namespace App\Http\Controllers;

use App\Contracts\AiClassificationServiceInterface;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Category;
use App\Models\Consulta;
use App\Models\LlmConfig;
use App\Models\PromptVersion;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    public function store(
        StoreConsultaRequest $request,
        AiClassificationServiceInterface $aiService
    ) {

        $customerMessage = $request
            ->validated()['customer_message'];


        $result = $aiService->classify($customerMessage);


        /*
         * FastAPI devolvió ERROR / TIMEOUT / NO_CREDITS
         */
        if (! $result->ok) {

            return redirect('/')
                ->with(
                    'error',
                    $result->errorMessage
                );
        }


        /*
         * FastAPI devolvió SUCCESS pero la IA clasificó como INVALID
         */
        if (in_array('INVALID', $result->categories, true)) {

            return redirect('/')
                ->with(
                    'error',
                    'El mensaje no se ha guardado porque parece ilegible. Por favor, asegúrate de que la elaboración de la consulta es adecuada.'
                );
        }


        /*
         * Solo aquí guardamos.
         */
        DB::transaction(function () use (
            $result,
            $customerMessage
        ) {

            $llmConfig = LlmConfig::query()
                ->where('provider', $result->provider)
                ->where('model', $result->model)
                ->firstOrFail();


            $promptVersion = PromptVersion::query()
                ->where('version', $result->promptVersion)
                ->firstOrFail();


            $consulta = Consulta::create([
                'llm_config_id' => $llmConfig->id,
                'prompt_version_id' => $promptVersion->id,
                'customer_message' => $customerMessage,
                'status' => $result->status,
            ]);


            $categoryIds = Category::query()
                ->whereIn(
                    'name',
                    $result->categories
                )
                ->pluck('id');


            $consulta
                ->categories()
                ->attach($categoryIds);
        });


        return redirect('/')
            ->with(
                'success',
                'Consulta clasificada y guardada correctamente.'
            );
    }
}