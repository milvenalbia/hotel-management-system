<?php

namespace App\Livewire;

use App\Models\Guest;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Redirect;

class CheckOutNotification extends Component
{
    public function render()
    {
        return view('livewire.top-navbar.check-out-notification');
    }

    public $count;
    public $guestNames = [];

    public function mount()
    {
        $today = now()->toDateString();
        $checkinsAndCheckouts = BookingTransaction::where('user_id', auth()->user()->id)
        ->whereDate('check_out', $today)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->get();

        $this->count = count($checkinsAndCheckouts);

        foreach ($checkinsAndCheckouts as $record) {
            $this->guestNames[] = [
                'first_name' => $record->guest->firstname,
                'last_name' => $record->guest->lastname,
                'check_out' => $record->formatted_check_out = Carbon::parse($record->check_out)->format('M j, Y'),
                'check_out_status' => $record->check_out_status,
            ]; 

        }

    }

    public function goToPage($id){

        $guest = Guest::where('id', $id)->first();

        $byStatus = $guest->firstname . ' ' . $guest->lastname;

        return Redirect::to(route('checkInGuest', ['byStatus' => $byStatus]));
    }
}
