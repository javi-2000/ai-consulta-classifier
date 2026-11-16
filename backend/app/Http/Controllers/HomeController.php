<?php

namespace App\Http\Controllers;

use App\Models\Consulta;

class HomeController extends Controller
{
    public function index()
    {
        $consultas = Consulta::with([
            'llmConfig',
            'promptVersion',
            'categories',
        ])
        ->latest()
        ->get();

        return view('home', compact('consultas'));
    }
}