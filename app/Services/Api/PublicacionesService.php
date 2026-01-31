<?php

namespace App\Services\Api;

use App\Models\Publicador;
use Illuminate\Support\Facades\DB;
//request


class PublicacionesService
{
    //subtipo evento para solo carrusel
    private $solocarousel = 10;
    /**
     * Query base para noticias con imagen
     */
    private function baseNoticiasQuery(array $selectFields = [])
    {
        $defaultFields = ['HN_MOB_PUBLICADOR.codigo', 'HN_MOB_PUBLICADOR.titulo', 'HN_MOB_PUBLICADOR.descripcion'];
        $fields = !empty($selectFields) ? $selectFields : $defaultFields;
        
        return Publicador::query()
            ->select($fields)
            ->addSelect([
                'imagen' => function ($query) {
                    $query->selectRaw("'http://visor-hn.hnacional.com/hn-mobile-publicador/'||ruta_archivo")
                        ->from('HN_MOB_PUBLICADOR_IMG')
                        ->whereColumn('HN_MOB_PUBLICADOR_IMG.EVENTO', 'HN_MOB_PUBLICADOR.CODIGO')
                        ->limit(1);
                }
            ])
            ->where('HN_MOB_PUBLICADOR.activo', 'S')
            ->where('HN_MOB_PUBLICADOR.tipo_evento', 2);
    }

    /**
     * Aplicar filtros a la query de noticias
     */
    private function aplicarFiltros($query, array $filtros = [])
    {
        if (isset($filtros['compania_id'])) {
            $query->where('HN_MOB_PUBLICADOR.compania', $filtros['compania_id']);
        }

        if (isset($filtros['codigo'])) {
            $query->where('HN_MOB_PUBLICADOR.codigo', $filtros['codigo']);
        }

        if (isset($filtros['exclude_codigo'])) {
            $query->where('HN_MOB_PUBLICADOR.codigo', '!=', $filtros['exclude_codigo']);
        }

        if (isset($filtros['subtipo_evento'])) {
            $query->where('HN_MOB_PUBLICADOR.subtipo_evento', $filtros['subtipo_evento']);
        }

        if (isset($filtros['vigentes']) && $filtros['vigentes']) {
            $query->whereRaw('HN_MOB_PUBLICADOR.fecha_inicio < SYSDATE')
                  ->whereRaw('HN_MOB_PUBLICADOR.fecha_fin > SYSDATE');
        }

        if (isset($filtros['carousel']) && $filtros['carousel']) {
            $query->whereRaw("HN_MOB_PUBLICADOR.presentador = 'S'");
        }

        if (isset($filtros['exclude_subtipo'])) {
            $query->where('HN_MOB_PUBLICADOR.subtipo_evento', '!=', $filtros['exclude_subtipo']);
        }

        if (isset($filtros['con_imagen']) && $filtros['con_imagen']) {
            $query->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('HN_MOB_PUBLICADOR_IMG')
                    ->whereColumn('HN_MOB_PUBLICADOR_IMG.EVENTO', 'HN_MOB_PUBLICADOR.CODIGO');
            });
        }

        if (isset($filtros['order_by'])) {
            $direction = $filtros['order_direction'] ?? 'ASC';
            $query->orderBy($filtros['order_by'], $direction);
        }

        if (isset($filtros['limit'])) {
            $query->limit($filtros['limit']);
        }

        return $query;
    }

    /**
     * Obtener últimas noticias
     */
    public function ultimasNoticias(int $companiaId)
    {
        $query = $this->baseNoticiasQuery();
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'exclude_subtipo' => $this->solocarousel,
            'vigentes' => true,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'ASC',
            'limit' => 4
        ])->get();
    }

    /**
     * Obtener todas las noticias
     */
    public function getNoticias(int $companiaId)
    {
        $query = $this->baseNoticiasQuery();
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'exclude_subtipo' => $this->solocarousel,
            'vigentes' => true,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'ASC'
        ])->get();
    }

    /**
     * Obtener una noticia específica por ID
     */
    public function getNoticiaById(int $id, int $companiaId)
    {
        $selectFields = [
            'HN_MOB_PUBLICADOR.codigo',
            'HN_MOB_PUBLICADOR.titulo',
            'HN_MOB_PUBLICADOR.descripcion',
            'HN_MOB_PUBLICADOR.fecha_inicio',
            'HN_MOB_PUBLICADOR.fecha_fin',
            'HN_MOB_PUBLICADOR.subtipo_evento',
            'HN_MOB_PUBLICADOR.tipo_evento'
        ];
        
        $query = $this->baseNoticiasQuery($selectFields);
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'codigo' => $id
        ])->first();
    }

    /**
     * Obtener otras noticias excluyendo la actual
     */
    public function getOtrasNoticias(int $excludeId, int $companiaId, int $limit = 4)
    {
        $query = $this->baseNoticiasQuery();
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'exclude_codigo' => $excludeId,
            'exclude_subtipo' => $this->solocarousel,
            'vigentes' => true,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'DESC',
            'limit' => $limit
        ])->get();
    }

    /**
     * Obtener noticias filtradas por subtipo de evento
     */
    public function noticiasPorSubTipo(int $companiaId, int $subTipoEvento)
    {
        $query = $this->baseNoticiasQuery();
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'subtipo_evento' => $subTipoEvento,
            'exclude_subtipo' => $this->solocarousel,
            'vigentes' => true,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'DESC',
            'limit' => 4
        ])->get();
    }

    /**
     * Obtener noticias paginadas
     */
    public function getNoticiasPaginadas(int $companiaId, int $perPage = 10)
    {
        $query = $this->baseNoticiasQuery([
            'HN_MOB_PUBLICADOR.codigo',
            'HN_MOB_PUBLICADOR.titulo',
            'HN_MOB_PUBLICADOR.descripcion',
            'HN_MOB_PUBLICADOR.fecha_inicio'
        ]);
        
        return $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'vigentes' => true,
            'exclude_subtipo' => $this->solocarousel,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'DESC'
        ])->paginate($perPage);
    }

    /**
     * Obtener noticias Carousel
     */
    public function noticiasCarousel(int $companiaId)
    {
        $query = $this->baseNoticiasQuery();

        $filtered = $this->aplicarFiltros($query, [
            'compania_id' => $companiaId,
            'vigentes' => true,
            'carousel' => true,
            'con_imagen' => true,
            'order_by' => 'HN_MOB_PUBLICADOR.fecha_inicio',
            'order_direction' => 'DESC',
            'limit' => 4
        ]);

        $results = $filtered->get();

        return $results;
    }

     public function ultimasNacional(int $companiaId, int $codigo = null)
    {
        $query = Publicador::query()
            ->select('HN_MOB_PUBLICADOR.codigo',
            'HN_MOB_PUBLICADOR.titulo',
            'HN_MOB_PUBLICADOR.descripcion',
            'HN_MOB_PUBLICADOR.fecha_inicio',
            'HN_MOB_PUBLICADOR.fecha_fin',
            'HN_MOB_PUBLICADOR.subtipo_evento',
            'HN_MOB_PUBLICADOR.tipo_evento' )
            ->addSelect([
                'imagen' => function ($query) {
                    $query->selectRaw("'http://visor-hn.hnacional.com/hn-mobile-publicador/'||ruta_archivo")
                        ->from('HN_MOB_PUBLICADOR_IMG')
                        ->whereColumn('HN_MOB_PUBLICADOR_IMG.EVENTO', 'HN_MOB_PUBLICADOR.CODIGO')
                        ->whereRaw("HN_MOB_PUBLICADOR_IMG.SN_ACTIVO = 'S'")
                        ->limit(1);
                }
            ])
            ->where('HN_MOB_PUBLICADOR.activo', 'S')
            ->where('HN_MOB_PUBLICADOR.compania', $companiaId)
            ->where('HN_MOB_PUBLICADOR.tipo_evento', 7)
            ->whereRaw('HN_MOB_PUBLICADOR.fecha_inicio < SYSDATE');
            //->whereRaw('HN_MOB_PUBLICADOR.fecha_fin > SYSDATE')

        // Si se proporciona un código específico, filtrar por ese código
        if ($codigo) {
            return $query->where('HN_MOB_PUBLICADOR.codigo', $codigo)->first();
        }

        // Si no hay código, retornar las últimas 4
        return $query->orderBy('HN_MOB_PUBLICADOR.fecha_inicio', 'DESC')
            ->limit(4)
            ->get();
    }

}
