<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\SubTipoEventoService;
use Illuminate\Http\Request;


class SubTipoEventoController extends Controller
{
    protected $service;

    public function __construct(SubTipoEventoService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => $this->service->index()
        ]);
    }

    public function noticias(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->service->getSubTiposNoticias($compania)
        ]);
    }
}
