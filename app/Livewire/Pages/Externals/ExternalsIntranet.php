<?php

namespace App\Livewire\Pages\Externals;

use Livewire\Component;
use App\Services\Api\HerramientasService;

class ExternalsIntranet extends Component
{
    public $tools = [];
    public $clientdata = [];
    public $hostname = '';
    public $searchTerm = '';

    public function mount($tools = [])
    {
        $HerramientasService = new HerramientasService();
        $this->clientdata = $HerramientasService->HostnameNTLM();

        if(isset($this->clientdata['data'][0]['hostname'])) {
            $this->hostname = $this->clientdata['data'][0]['hostname'];
        }

        $this->clientdata = $HerramientasService->HostnameNTLM();
        $this->tools = $HerramientasService->appexternasPorEquipo("gnavarro");
    }

    /**
     * Computed property: herramientas filtradas
     */
    public function getFilteredToolsProperty()
    {
        $filtered = collect($this->tools);
        if (!empty($this->searchTerm)) {
            $searchLower = strtolower($this->searchTerm);
            $filtered = $filtered->filter(function($tool) use ($searchLower) {
                foreach (get_object_vars($tool) as $prop => $value) {
                    $propLower = strtolower($prop);
                    if (($propLower === 'nombre' || $propLower === 'descripcion_corta') 
                        && stripos($value, $this->searchTerm) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        return $filtered->values()->all();
    }

    public function render()
    {
        return view('livewire.pages.externals.externals-intranet');
    }
}
