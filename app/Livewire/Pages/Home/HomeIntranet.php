<?php

namespace App\Livewire\Pages\Home;
use Livewire\Component;
use App\Services\Api\PublicacionesService;
use App\Services\Api\HerramientasService;

class HomeIntranet extends Component
{   
    public $companyId = 1;
    public $carouselNoticias = [];

    public $nombre = '';
    public $nombres = ["alfredo"];
    public $figmaColors = [];
    public $assets = [];
    public $news = [];
    public $events = [];
    public $hrRequests = [];
    public $shortcuts = [];
    public $tools = [];
    public $clientdata = [];
    public $hostname = '';

    public function mount()
    {
        $this->companyId = $this->companyId ?? 1;
        $this->loadCarouselNoticias();
    }

    protected function loadCarouselNoticias()
    {
        $publicacionesService = new PublicacionesService();
        $this->carouselNoticias = $publicacionesService->noticiasCarousel(1); // companiaId = 1

        $HerramientasService = new HerramientasService();
        $this->clientdata = $HerramientasService->HostnameNTLM();
        if(isset($this->clientdata['data'][0]['hostname'])) {
            $this->hostname = $this->clientdata['data'][0]['hostname'];
        }
        $this->shortcuts = $HerramientasService->herramientasPorEquipo($this->hostname,5); // Suponiendo companiaId = 1, rolId = 5, limit = 5;
    }

    public function render()
    {
        return view('livewire.pages.home-intranet', [
            'carouselNoticias' => $this->carouselNoticias,
            'companiaId' => $this->companyId
        ]);
    }
}
