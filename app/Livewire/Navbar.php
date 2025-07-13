<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Navbar extends Component
{
    public $currentDateTime;

    public function render()
    {
        return view('livewire.top-navbar.navbar');

        $this->updateTime();
    }

    public function openModal(){
        $this->dispatch('open-modal');
    }

    public function cancellation(){
        $this->dispatch('cancellation');
    }

    public function logouts(){
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return Redirect::to('/login')->with('success', 'You have been logged out!');
    }

    public function mount()
    {
        $this->currentDateTime = now()->format('l, F j, Y');
    }
}
