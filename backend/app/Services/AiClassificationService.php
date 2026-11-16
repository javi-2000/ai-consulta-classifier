<?php

namespace App\Services;

use App\Contracts\AiClassificationServiceInterface;
use App\Enums\ConsultaStatus;
use App\Services\AiClassification\AiClassificationResult;
use Illuminate\Support\Facades\Http;

class AiClassificationService implements AiClassificationServiceInterface
{
    public function classify(string $customerMessage): AiClassificationResult
    {
        try {
            $response = Http::timeout(
                config('services.ai.timeout')
            )->post(
                    config('services.ai.url') . '/classify',
                    [
                        'customer_message' => $customerMessage,
                    ]
                );

        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            return AiClassificationResult::error(
                status: ConsultaStatus::Timeout,
                errorCode: 'AI_TIMEOUT',
                errorMessage: 'El servicio de inteligencia artificial no está disponible.',
            );
        }


        if (!$response->successful()) {

            return AiClassificationResult::error(
                status: ConsultaStatus::Error,
                errorCode: 'AI_HTTP_ERROR',
                errorMessage: 'El servicio de inteligencia artificial devolvió un error.',
            );
        }


        $data = $response->json();


        if (!is_array($data) || !isset($data['status'])) {

            return AiClassificationResult::error(
                status: ConsultaStatus::Error,
                errorCode: 'AI_INVALID_RESPONSE',
                errorMessage: 'Respuesta inválida del servicio de inteligencia artificial.',
            );
        }


        /*
         * FastAPI devuelve:
         *
         * SUCCESS
         * ERROR
         * TIMEOUT
         * NO_CREDITS
         */
        if ($data['status'] !== ConsultaStatus::Success->value) {

            $status = ConsultaStatus::tryFrom(
                $data['status']
            );

            return AiClassificationResult::error(
                status: $status ?? ConsultaStatus::Error,
                errorCode: $data['error_code'] ?? null,
                errorMessage: $data['error_message']
                ?? 'Error desconocido del servicio de inteligencia artificial.',
            );
        }


        /*
         * Validación de categorías
         *
         * SUCCESS siempre debe traer al menos una categoría.
         */
        $categories = $data['categories'] ?? null;


        if (!is_array($categories) || count($categories) === 0) {

            return AiClassificationResult::error(
                status: ConsultaStatus::Error,
                errorCode: 'AI_EMPTY_CATEGORIES',
                errorMessage: 'La inteligencia artificial no devolvió categorías.',
            );
        }


        /*
         * Validamos campos obligatorios del modelo
         */
        if (
            empty($data['provider']) ||
            empty($data['model']) ||
            empty($data['prompt_version'])
        ) {

            return AiClassificationResult::error(
                status: ConsultaStatus::Error,
                errorCode: 'AI_MISSING_METADATA',
                errorMessage: 'La respuesta de inteligencia artificial no contiene información completa del modelo.',
            );
        }


        return AiClassificationResult::success(
            categories: $categories,
            provider: $data['provider'],
            model: $data['model'],
            promptVersion: $data['prompt_version'],
        );
    }
}