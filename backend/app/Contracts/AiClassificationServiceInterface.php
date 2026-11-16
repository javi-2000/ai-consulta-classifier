<?php

namespace App\Contracts;

use App\Services\AiClassification\AiClassificationResult;

interface AiClassificationServiceInterface
{
    public function classify(string $customerMessage): AiClassificationResult;
}
