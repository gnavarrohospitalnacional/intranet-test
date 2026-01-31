<?php

namespace App\Services\Api;

use App\Models\SubTipoEvento;
use App\Models\TipoEvento;
use Illuminate\Support\Facades\DB;

class SubTipoEventoService
{

    //subtipo evento para solo carrusel
    private $solocarousel = 10;

    public function index()
    {
        $query = SubTipoEvento::with(['compania','tipo_evento']);

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->tipo_evento);
        }

        return $query->get();

    }
        /**
     * Obtener subtipos de eventos de noticias con publicaciones activas
     */
    public function getSubTiposNoticias(int $companiaId)
    {
        return TipoEvento::query()
            ->select('HN_MOB_SUBTIPO_EVENTO.codigo', 'HN_MOB_SUBTIPO_EVENTO.titulo')
            ->distinct()
            ->join('HN_MOB_PUBLICADOR', 'HN_MOB_PUBLICADOR.tipo_evento', '=', 'HN_MOB_TIPO_EVENTO.codigo')
            ->join('HN_MOB_SUBTIPO_EVENTO', 'HN_MOB_SUBTIPO_EVENTO.codigo', '=', 'HN_MOB_PUBLICADOR.subtipo_evento')
            ->where('HN_MOB_PUBLICADOR.activo', 'S')
            ->where('HN_MOB_PUBLICADOR.compania', $companiaId)
            ->where('HN_MOB_PUBLICADOR.tipo_evento', 2)
            ->whereRaw('HN_MOB_PUBLICADOR.subtipo_evento != ' . $this->solocarousel)
            ->whereRaw('HN_MOB_PUBLICADOR.fecha_inicio < SYSDATE')
            ->whereRaw('HN_MOB_PUBLICADOR.fecha_fin > SYSDATE')
            ->orderBy('HN_MOB_SUBTIPO_EVENTO.titulo')
            ->get();
    }
}
