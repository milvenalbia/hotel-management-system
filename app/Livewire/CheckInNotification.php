<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;
use App\Models\Guest;
use Illuminate\Support\Facades\Redirect;

class CheckInNotification extends Component
{

    public $count;
    public $guestNames = [];

    protected $listeners = ['updateNotification' => 'mount'];
    
    public function mount()
{
    $this->refreshComponent();
}

public function refreshComponent()
{
    $today = now()->toDateString();
    $checkinsAndCheckouts = BookingTransaction::where('user_id', auth()->user()->id)
    ->whereDate('check_in', $today)
    ->where('check_in_status', false)
    ->where('check_out_status', false)
    ->where('cancel_status', false)
    ->get();

    $this->count = count($checkinsAndCheckouts);
    $this->guestNames = [];

    foreach ($checkinsAndCheckouts as $record) {
        $this->guestNames[] = [
            'id' => $record->guest->id,
            'first_name' => $record->guest->firstname,
            'last_name' => $record->guest->lastname,
            'check_in_date' => $record->formatted_check_in = Carbon::parse($record->check_in)->format('M j, Y'),
            'check_in_status' => $record->check_in_status,
        ];
    }
}

    public function render()
    {
        return view('livewire.top-navbar.check-in-notification');
    }

    public function goToPage($id){

        $guest = Guest::where('id', $id)->first();

        $byStatus = $guest->firstname . ' ' . $guest->lastname;

        return Redirect::to(route('checkInGuest', ['byStatus' => $byStatus]));
    }
}
