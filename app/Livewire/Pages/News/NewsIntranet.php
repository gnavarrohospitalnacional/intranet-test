<?php

namespace App\Livewire\Pages\News;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Api\PublicacionesService;

class NewsIntranet extends Component
{
    use WithPagination;

    public function render()
    {
        $publicacionesService = new PublicacionesService();
        
        $noticias = $publicacionesService->getNoticiasPaginadas(1, 6);
        
        return view('livewire.pages.news.news-intranet', [
            'noticias' => $noticias
        ]);
    }
}
