<?php

namespace App\Models;

use App\Enums\ConsultaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Consulta extends Model
{
    protected $fillable = [
        'llm_config_id',
        'prompt_version_id',
        'customer_message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConsultaStatus::class,
        ];
    }

    public function llmConfig(): BelongsTo
    {
        return $this->belongsTo(LlmConfig::class);
    }

    public function promptVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
