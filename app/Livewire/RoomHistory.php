<?php

namespace App\Livewire;

use App\Models\BookingTransaction;
use App\Models\RoomBooking;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;

class RoomHistory extends Component
{
    public function render()
    {
        return view('livewire.room-management.room-history');
    }

    public $guest_data = [];
    public $check_in;
    public $check_out;
    public $by_date = 'monthly';
    public $roomId;
    public $bookingTransaction;

    #[On('room-guest-history')]
    public function openModal($roomId){

        $this->roomId = $roomId;

        $this->updatedByDate();
        $this->dispatch('show-room-history-modal');
    }

    public function close(){

        $this->reset();

        $this->dispatch('close-room-modal');
    }

    public function openFolio($id){

        $this->dispatch('guest-folio', guestId: $id);
    }

    public function updatedByDate(){

        $query = RoomBooking::where('room_id', $this->roomId);

        $roomBookings = $query->get();

        $this->guest_data = [];

        foreach ($roomBookings as $roomBooking) {

            $query = BookingTransaction::where('user_id', auth()->user()->id)
            ->where('id', $roomBooking->booking_transaction_id)
            ->where('check_out_status', true);
            if ($this->by_date == 'today') {
                $query->whereDate('updated_at', now()->format('Y-m-d'));
            } elseif ($this->by_date == 'weekly') {
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->by_date == 'monthly') {
                $query->whereMonth('updated_at', now()->month);
            } elseif ($this->by_date == 'yearly') {
                $query->whereYear('updated_at', now()->year);
            }
            $this->bookingTransaction = $query->first();

            if ($this->bookingTransaction) {
                $this->guest_data[] = [
                    'id' => $this->bookingTransaction->id,
                    'folio' => $this->bookingTransaction->folio_no,
                    'name' => $this->bookingTransaction->guest->firstname . ' ' . $this->bookingTransaction->guest->lastname,
                    'contact_no' => $this->bookingTransaction->guest->contact_no,
                    'email' => $this->bookingTransaction->guest->email,
                    'check_in' => Carbon::parse($this->bookingTransaction->check_in)->format('F j, Y'),
                    'check_out' => Carbon::parse($this->bookingTransaction->check_out)->format('F j, Y'),
                    'check_in_status' => $this->bookingTransaction->check_in_status,
                    'check_out_status' => $this->bookingTransaction->check_out_status,
                ];
            }
        }
    }
}
