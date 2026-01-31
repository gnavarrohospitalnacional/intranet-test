<?php

namespace App\Livewire\Pages\Nacional;

use Livewire\Component;
use App\Services\Api\PublicacionesService;

class NacionalIntranet extends Component
{
    public $nacionalItems = [];
    public $selectedNacional = null;
    public $selectedNacionalId = null;
    public $selectedPdfUrl = null;
    public $selectedTitle = 'La Nacional';
    public $loadingPdf = false;
    public $codigo = null;

    public function mount($codigo = null)
    {
        $service = new PublicacionesService();
        $this->codigo = $codigo;
        if ($codigo) {
            $this->selectedNacional = $service->ultimasNacional(1, $codigo);
            $this->nacionalItems = $service->ultimasNacional(1);
            //dd( $this->selectedNacional, $this->nacionalItems );
            if ($this->selectedNacional) {
                $this->selectItem($this->selectedNacional);
            } else {
                $this->selectFirstItem();
            }
        } else {
            $this->nacionalItems = $service->ultimasNacional(1);
            $this->selectFirstItem();
        }
    }

    public function selectNacional($codigo)
    {
        if ($this->selectedNacional && $this->selectedNacional->codigo == $codigo) {
            return;
        }

        $this->loadingPdf = true;
        
        // Buscar en los items cargados
        $item = collect($this->nacionalItems)->firstWhere('codigo', $codigo);
        
        if ($item) {
            $this->selectedNacional = $item;
            $this->selectItem($item);
        }
        
        $this->loadingPdf = false;
    }

    public function selectFirstItem()
    {
        if (!empty($this->nacionalItems)) {
            $this->selectedNacional = $this->nacionalItems[0];
            $this->selectItem($this->nacionalItems[0]);
        }
    }

    private function selectItem($item)
    {
        $this->selectedNacionalId = $item->codigo ?? $item['codigo'];
        $this->selectedPdfUrl = url('/proxy-pdf?url=' . urlencode($item->imagen ?? $item['imagen']));
        $this->selectedTitle = $item->titulo ?? $item['titulo'];

        // Avisar a JS que cargue el PDF
        $this->dispatch(
            'load-nacional-pdf',
            pdf: $this->selectedPdfUrl,
            title: $this->selectedTitle
        );
    }

    public function render()
    {
        return view('livewire.pages.nacional.nacional-intranet');
    }
}
