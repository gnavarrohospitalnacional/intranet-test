<?php

namespace App\Services\Api;

use App\Models\TipoEvento;
use Illuminate\Support\Facades\DB;

class TipoEventoService
{
    public function index()
    {
        return TipoEvento::query()
            ->select('codigo', 'titulo')
            ->orderBy('titulo')
            ->get();
    }
    /**
     * Obtener subtipos de eventos con publicadores activos
     */
    public function getTipoEventosActivos(int $companiaId)
    {
        return TipoEvento::query()
            ->select('HN_MOB_TIPO_EVENTO.codigo', 'HN_MOB_TIPO_EVENTO.titulo')
            ->distinct()
            ->join('HN_MOB_PUBLICADOR', 'HN_MOB_PUBLICADOR.tipo_evento', '=', 'HN_MOB_TIPO_EVENTO.codigo')
            ->join('HN_MOB_SUBTIPO_EVENTO', 'HN_MOB_SUBTIPO_EVENTO.codigo', '=', 'HN_MOB_PUBLICADOR.subtipo_evento')
            ->where('HN_MOB_PUBLICADOR.activo', 'S')
            ->where('HN_MOB_PUBLICADOR.compania', $companiaId)
            ->whereRaw('HN_MOB_PUBLICADOR.fecha_inicio < SYSDATE')
            ->whereRaw('HN_MOB_PUBLICADOR.fecha_fin > SYSDATE')
            ->orderBy('HN_MOB_TIPO_EVENTO.titulo')
            ->get();
    }
}
