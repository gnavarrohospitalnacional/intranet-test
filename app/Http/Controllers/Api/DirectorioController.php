<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\DirectorioService;

class DirectorioController extends Controller
{

    protected $directorioService;

    public function __construct(DirectorioService $directorioService)
    {
        $this->directorioService = $directorioService;
    }   

    public function allDirectorio(Request $request)
    {
        return response()->json([

            'data' => $this->directorioService->allDirectorio()

        ]);
    }

    //ver datos del LDAP del servicio de directorio
    public function getLDAP(Request $request)
    {
        return response()->json([

            'data' => $this->directorioService->getAllUsers()

        ]);
    }
}
