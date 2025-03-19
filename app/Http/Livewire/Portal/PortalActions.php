<?php

namespace App\Http\Livewire\Portal;

use Livewire\Component;

class PortalActions extends Component
{
    public $portal;
    public $pageTitle;
    public $data;

    public function mount($portal,$pageTitle,$data){
        $this->portal = $portal;
        $this->data = $data;
        $this->pageTitle = $pageTitle;
    }
    public function render()
    {
        return view('livewire.portal.portal-actions');
    }
}
