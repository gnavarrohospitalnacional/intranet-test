<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\PublicacionesService;
use App\Services\Api\HerramientasService;

class HerramientasController extends Controller
{

    protected $herramientasService;

    public function __construct(HerramientasService $herramientasService)
    {
        $this->herramientasService = $herramientasService;
    } 
   
    public function shortcutsHerramientas(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->herramientasService->herramientasPorEquipo('gnavarro',5,5)
        ]);
    }

    public function Herramientas(Request $request)
    {
        $compania = $request->get('compania', 1);
        return response()->json([
            'data' => $this->herramientasService->herramientasPorEquipo('gnavarro',5,100)
        ]);
    }

    public function Hostname()
    {
        return $this->herramientasService->HostnameNTLM();
    }

    public function hostname(Request $request)
    {
        $request->ip();
        $hostname = $this->herramientasService->HostnameNTLM($request);

        return response()->json([
            'ip' => $request->ip(),
            'hostname' => $hostname,
        ]);
    }
}
