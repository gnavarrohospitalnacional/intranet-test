<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\PublicacionesService;

class PublicadorController extends Controller
{

    protected $publicadorService;

    public function __construct(PublicacionesService $publicadorService)
    {
        $this->publicadorService = $publicadorService;
    }    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->publicadorService->listar();
    }

    public function noticias(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->publicadorService->getNoticias($compania)
        ]);
    }

    public function ultimasNoticias(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->publicadorService->ultimasNoticias($compania)
        ]);
    }

    public function noticiasCarousel(Request $request)
    {
        $compania = $request->get('compania', 1);
        $data = $this->publicadorService->noticiasCarousel($compania);      
        return response()->json([
            'data' => $data
        ]);
    }
}
