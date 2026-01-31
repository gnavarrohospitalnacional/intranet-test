<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TipoEventoController;
use App\Http\Controllers\Api\SubTipoEventoController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\CompaniaController;
use App\Http\Controllers\Api\PublicadorController;
use App\Http\Controllers\Api\Compania;
use App\Http\Controllers\Api\HerramientasController;
use App\Http\Controllers\Api\DirectorioController;

Route::prefix('v1')->group(function () {
    Route::get('companias', [CompaniaController::class, 'index']);
    Route::get('tipos-eventos', [TipoEventoController::class, 'index']);
    Route::get('subtipos-evento', [SubTipoEventoController::class, 'index']);
    Route::apiResource('publicadores', PublicadorController::class);
    Route::get('subtipos-eventos/noticias', [SubTipoEventoController::class, 'noticias']);
    Route::get('tipos-eventos/activos', [TipoEventoController::class, 'activos']);
    Route::get('publicaciones/noticias', [PublicadorController::class, 'noticias']);
    Route::get('publicaciones/ultimas-noticias', [PublicadorController::class, 'ultimasNoticias']);
    Route::get('herramientas/shortcuts', [HerramientasController::class, 'shortcutsHerramientas']);
    Route::get('herramientas/hostname', [HerramientasController::class, 'Hostname']);
    Route::get('directorio', [DirectorioController::class, 'allDirectorio']);
    Route::get('directorio/ldap', [DirectorioController::class, 'getLDAP']);
    Route::get('publicaciones/carousel', [PublicadorController::class, 'noticiasCarousel']);
});
