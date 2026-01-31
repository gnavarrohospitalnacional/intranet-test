<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\Api\SubTipoEventoService;
use App\Services\Api\PublicacionesService;
use App\Services\Api\HerramientasService;

class NewsSection extends Component
{
    public $news = [];
    public $images1;

    //  NUEVO
    public $tiposEventos = [];
    public $tipoSeleccionado = 'all';
    public $noticias = [];
    public $noticiaSeleccionada = null;
    public $shortcuts = [];
    public $clientdata = [];
    public $hostname = '';

    /**
     * Se ejecuta al montar el componente
     */
    public function mount($news = [], $images1 = null)
    {
        $this->tipoSeleccionado = session()->get('tipo_evento', 'all');

        $this->images1 = $images1;

        // Cargar tipos de eventos desde el servicio
        $SubtipoEventoService = new SubTipoEventoService();
        $this->tiposEventos = $SubtipoEventoService->getSubTiposNoticias(1); // Suponiendo companiaId = 1;

        // Cargar últimas noticias desde el servicio
        $PublicacionesService = new PublicacionesService();
        $this->noticias = $PublicacionesService->ultimasNoticias(1); // Suponiendo companiaId = 1;

}

    /**
     * Seleccionar tipo de evento
     */
    public function seleccionarTipo($codigo)
    {
        $this->tipoSeleccionado = $codigo;
        session()->put('tipo_evento', $codigo);
        
        // Recargar las noticias según el tipo seleccionado
        $this->cargarNoticias();
    }

    /**
     * Cargar noticias según el tipo seleccionado
     */
    public function cargarNoticias()
    {
        // Recargar tipos de eventos para evitar que se pierdan
        $SubtipoEventoService = new SubTipoEventoService();
        $this->tiposEventos = $SubtipoEventoService->getSubTiposNoticias(1);
        
        $PublicacionesService = new PublicacionesService();
        
        if ($this->tipoSeleccionado === 'all') {
            $this->noticias = $PublicacionesService->ultimasNoticias(1);
        } else {
            $this->noticias = $PublicacionesService->noticiasPorSubTipo(1, $this->tipoSeleccionado);
        }
    }

    public function render()
    {
        return view('livewire.news-section');
    }
}
