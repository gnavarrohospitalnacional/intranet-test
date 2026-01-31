<?php

namespace App\Livewire\Pages\Academy;

use Livewire\Component;

class AcademyIntranet extends Component
{
    public $courses = [];

    public function mount()
    {
        $this->courses = config('academy.courses', []);
    }

    public function render()
    {
        return view('livewire.pages.academy.academy-intranet');
    }
}
