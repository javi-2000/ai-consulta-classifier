<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PromptVersion extends Model
{

    protected $fillable = [
        'version',
        'prompt'
    ];


    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }

}