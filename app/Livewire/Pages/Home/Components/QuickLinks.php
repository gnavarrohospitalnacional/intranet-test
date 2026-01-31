<?php

namespace App\Livewire\Pages\Home\Components;

use Livewire\Component;
use App\Services\Api\HerramientasService;

class QuickLinks extends Component
{
    public $shortcuts = [];
    public $clientdata = [];
    public $hostname = '';

    /**
     * Se ejecuta al montar el componente
     */
    public function mount($shortcuts = [])
    {

        // Cargar tipos de eventos desde el servicio
        $HerramientasService = new HerramientasService();

        $this->clientdata = $HerramientasService->HostnameNTLM();

        if(isset($this->clientdata['data'][0]['hostname'])) {
            $this->hostname = $this->clientdata['data'][0]['hostname'];
        }

        $this->shortcuts = $HerramientasService->herramientasPorEquipo($this->hostname,5); // Suponiendo companiaId = 1, rolId = 5, limit = 5;

    }

    public function render()
    {
        return view('livewire.pages.home.components.quick-links');
    }
}
