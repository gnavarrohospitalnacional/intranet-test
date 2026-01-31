<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\TipoEventoService;
use Illuminate\Http\Request;

class TipoEventoController extends Controller
{
    protected $service;

    public function __construct(TipoEventoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'data' => $this->service->index()
        ]);
    }

    public function activos(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->service->getTipoEventosActivos($compania)
        ]);
    }
}
