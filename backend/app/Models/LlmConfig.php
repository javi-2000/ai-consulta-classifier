<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class LlmConfig extends Model
{

    protected $fillable = [
        'provider',
        'model',
        'temperature'
    ];


    protected $casts = [
        'temperature'=>'float'
    ];


    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }

}