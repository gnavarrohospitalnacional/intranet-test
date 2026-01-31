<?php

namespace App\Livewire\Pages\Tools;

use Livewire\Component;
use App\Services\Api\HerramientasService;

class ToolsIntranet extends Component
{
    public $tools = [];
    public $clientdata = [];
    public $hostname = '';
    public $ambientes = [];
    public $selectedAmbiente = '';
    public $searchTerm = '';

    public function mount($tools = [])
    {
        // Cargar tipos de eventos desde el servicio
        $HerramientasService = new HerramientasService();

        $this->clientdata = $HerramientasService->HostnameNTLM();

        if(isset($this->clientdata['data'][0]['hostname'])) {
            $this->hostname = $this->clientdata['data'][0]['hostname'];
        }

        $this->tools = $HerramientasService->herramientasPorEquipo($this->hostname,99);
        
        // Extraer ambientes únicos directamente de los objetos
        $ambientesMap = [];
        foreach ($this->tools as $tool) {
            // Buscar la propiedad sin importar el caso
            $codigo = null;
            $nombre = null;
            
            foreach (get_object_vars($tool) as $prop => $value) {
                if (strtoupper($prop) === 'CODIGOAMBIENTE') $codigo = $value;
                if (strtoupper($prop) === 'NOMBREAMBIENTE') $nombre = $value;
            }
            
            if ($codigo && !isset($ambientesMap[$codigo])) {
                $ambientesMap[$codigo] = ['codigo' => $codigo, 'nombre' => $nombre];
            }
        }
        
        $this->ambientes = collect($ambientesMap)
            ->sortBy('nombre')
            ->values()
            ->toArray();
        
        // Seleccionar el primer ambiente por defecto
        if (!empty($this->ambientes)) {
            $this->selectedAmbiente = $this->ambientes[0]['codigo'];
        }
    }

    /**
     * Computed property: herramientas filtradas
     */
    public function getFilteredToolsProperty()
    {
        $filtered = collect($this->tools);
        
        // Filtrar por ambiente si hay uno seleccionado
        if (!empty($this->selectedAmbiente)) {
            $filtered = $filtered->filter(function($tool) {
                foreach (get_object_vars($tool) as $prop => $value) {
                    if (strtoupper($prop) === 'CODIGOAMBIENTE' && $value == $this->selectedAmbiente) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        // Filtrar por término de búsqueda
        if (!empty($this->searchTerm)) {
            $searchLower = strtolower($this->searchTerm);
            $filtered = $filtered->filter(function($tool) use ($searchLower) {
                foreach (get_object_vars($tool) as $prop => $value) {
                    $propUpper = strtoupper($prop);
                    if (($propUpper === 'NOMBRE' || $propUpper === 'DESCRIPCION_CORTA') 
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
        return view('livewire.pages.tools.tools-intranet');
    }
}
