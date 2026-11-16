<?php

namespace App\Services\AiClassification;

use App\Enums\ConsultaStatus;

readonly class AiClassificationResult
{
    /**
     * @param list<string> $categories
     */
    public function __construct(
        public bool $ok,
        public ConsultaStatus $status,
        public array $categories = [],
        public ?string $provider = null,
        public ?string $model = null,
        public ?string $promptVersion = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
    ) {
    }


    /**
     * @param list<string> $categories
     */
    public static function success(
        array $categories,
        string $provider,
        string $model,
        string $promptVersion,
    ): self {

        return new self(
            ok: true,
            status: ConsultaStatus::Success,
            categories: $categories,
            provider: $provider,
            model: $model,
            promptVersion: $promptVersion,
        );
    }


    public static function error(
        ConsultaStatus $status,
        ?string $errorCode = null,
        ?string $errorMessage = null,
    ): self {

        return new self(
            ok: false,
            status: $status,
            categories: [],
            errorCode: $errorCode,
            errorMessage: $errorMessage,
        );
    }
}