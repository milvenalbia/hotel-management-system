<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Payment;
use Livewire\Component;
use App\Models\RoomBooking;
use Livewire\Attributes\On;
use App\Models\BookingTransaction;

class DeleteBookingModal extends Component
{
    public function render()
    {
        return view('livewire.manage-booking.modals.delete-booking-modal');
    }

    public $booking_id;
    public $firstname;
    public $lastname;
    public $guest_id;

    #[On('delete-modal')]
    public function deleteConfirmation($Id)
    {
        
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $Id)
        ->first();

        $guest = $booking->guest;

        $this->booking_id = $booking->id;
        $this->firstname = $guest->firstname;
        $this->lastname = $guest->lastname;
        $this->guest_id = $booking->guest_id;

        $this->dispatch('show-delete-booking-modal');
    }

    public function deleteData()
{
   
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $this->booking_id)
        ->first();

        if ($booking) {
            $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
            
            RoomBooking::where('booking_transaction_id', $this->booking_id)
            ->update([
                'check_in_status' => false,
                'check_out_status' => false,
                'booking_status' => false,
                'cancel_status' => true
            ]);
            
            $room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
            
            Room::where('user_id', auth()->user()->id)
            ->whereIn('id', $room_ids)
            ->whereNotExists(function ($subquery) {
                $subquery->selectRaw(1)
                    ->from('room_bookings')
                    ->whereColumn('room_bookings.room_id', 'rooms.id')
                    ->where(function ($q) {
                        $q->where(function ($qq) {
                            $qq->where('check_in_status', true)
                                ->orWhere('booking_status', true);
                        });
                    });
            })
            ->update(['status' => true, 'room_status' => 'Vacant Ready']);
        }

        $booking->update([
            'cancel_status' => true,
        ]);

        $this->dispatch('delete-success');

        $this->dispatch('updateNotification');

        $this->dispatch('close-guest-booking-modal');

        $this->booking_id = '';
}

    public function cancel()
    {
        $this->booking_id = '';
    }
}
