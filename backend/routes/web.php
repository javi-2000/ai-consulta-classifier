<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConsultaController;

Route::get('/', [HomeController::class, 'index']);

Route::post('/consultas', [ConsultaController::class, 'store']);