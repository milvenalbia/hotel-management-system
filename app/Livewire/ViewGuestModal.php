<?php

namespace App\Livewire;

use tidy;
use App\Models\Room;
use App\Models\User;
use App\Models\Guest;
use App\Models\Payment;
use Livewire\Component;
use App\Models\Roomtype;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;

class ViewGuestModal extends Component
{
    public function render()
    {
        return view('livewire.manage-booking.modals.view-guest-modal');
    }

    public $user_data = [];
    public $booking_data = [];
    public $dining_data = [];
    public $check_in;
    public $check_out;
    public $room_price;
    public $extend_hours_amount;
    public $extend_days_amount;
    public $nights;
    public $nights_amount;
    public $total_nights;
    public $total_nights_amount;
    public $book_sub_total;
    public $dine_sub_total;
    public $advance_payment;
    public $remaining_amount;
    public $tax;
    public $invoice_no;
    public $date_today;
    public $paid_amount;
    public $total_amount;
    public $booking_edit_id;
    public $payments;
    public $room_no = [];

    #[On('view-detail-modal')]
    public function viewDetailModal($bookId){

        $today = Carbon::now();

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        $user = User::where('id', auth()->user()->id)->first();

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $bookId)->first();

        $roomBooking = $booking->roomBooking->first();
        $room = $roomBooking->room;
        $roomtype = $room->roomtypes;

        $this->booking_edit_id = $booking->id;

        $this->payments = Payment::find($booking->payment_id);

        $guest = Guest::find($booking->guest_id);

        $this->user_data[] = [
            'hotel_name' => $user->hotel_name,
        ];

        $checkIn = Carbon::parse($booking->check_in);
        $checkOut = Carbon::parse($booking->check_out);

        // Calculate the stay duration based on check-in and check-out dates
        $this->nights = $checkIn->diffInDays($checkOut) - $booking->extend_days;

        $this->nights_amount= ($this->nights * $booking->room_price) * $booking->room;

        $this->total_nights = $this->nights + $booking->extend_days;

        $this->extend_hours_amount = ($booking->room_price * 0.05) * $booking->extend_hours;
        $this->extend_days_amount = $booking->room_price * $booking->extend_days;

        $this->total_nights_amount = $this->nights_amount + $this->extend_days_amount;

        $this->check_in = Carbon::parse($booking->check_in)->format('F j, Y');
        $this->check_out = Carbon::parse($booking->check_out)->format('F j, Y');

        $this->book_sub_total = $this->total_nights_amount + $this->extend_days_amount + $this->extend_hours_amount + $this->payments->extra_bed_amount;

        $rooms = $booking->roomBooking;

        $room_id = $rooms->pluck('room.id');

        $this->room_no = Room::where('user_id', auth()->user()->id)
        ->whereIn('id', $room_id)->get();

        $this->booking_data[] = [
            'folio' => $booking->folio_no,
            'firstname' => $guest->firstname,
            'lastname' => $guest->lastname,
            'phone' => $guest->contact_no,
            'email' => $guest->email,
            'roomtype' => $roomtype->roomtype,
            'room' => $booking->room,
            'extra_bed' => $booking->extra_bed,
            'extend_hours' => $booking->extend_hours,
            'extend_days' => $booking->extend_days,
            'extra_bed_amount' => $this->payments->extra_bed_amount,
        ];

        $this->room_price = $booking->room * $booking->room_price;

        $this->dispatch('view-guest-modal');
    }

    public function close()
    {
        $this->dispatch('close-guest-booking-modal');
        $this->reset();
        $this->dispatch('closing-view-guest-modal');
    }

}
