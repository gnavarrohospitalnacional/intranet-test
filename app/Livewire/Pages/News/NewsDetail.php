<?php

namespace App\Livewire\Pages\News;

use Livewire\Component;
use App\Services\Api\PublicacionesService;

class NewsDetail extends Component
{
    public $noticia;
    public $otrasNoticias;
    public $id;

    public function mount($id)
    {
        $this->id = $id;
        
        
        $publicacionesService = new PublicacionesService();
        $this->noticia = $publicacionesService->getNoticiaById($id, 1); // companiaId = 1

        if (!$this->noticia) {
            return redirect()->route('news');
        }

        $this->otrasNoticias = $publicacionesService->getOtrasNoticias($id, 1, 4);
    }

    public function render()
    {
        return view('livewire.pages.news.news-detail');
    }
}
