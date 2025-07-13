<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\User;
use App\Models\Guest;
use App\Models\Payment;
use Livewire\Component;
use App\Models\OrderItem;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\OrderTransaction;
use App\Models\BookingTransaction;

class GuestFolio extends Component
{
    public function render()
    {
        return view('livewire.room-management.guest-folio');
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
    public $room_id;
    public $room_no = [];

    #[On('guest-folio')]
    public function viewCheckOut($guestId){

        $today = Carbon::now();

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        $user = User::where('id', auth()->user()->id)->first();

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $guestId)->first();

        $roomBooking = $booking->roomBooking->first();
        $room = $roomBooking->room;
        $roomtype = $room->roomtypes;

        $this->booking_edit_id = $booking->id;

        $dining = OrderTransaction::where('user_id', auth()->user()->id)
        ->where('guest_id', $booking->guest_id)->get();

        $order_id = $dining->pluck('id');

        $this->payments = Payment::find($booking->payment_id);

        $this->invoice_no = $this->payments->invoice_no;

        $guest = Guest::find($booking->guest_id);

        $this->user_data[] = [
            'hotel_name' => $user->hotel_name,
        ];

        $checkIn = Carbon::parse($booking->check_in);
        $checkOut = Carbon::parse($booking->check_out);

        // Calculate the stay duration based on check-in and check-out dates
        $this->nights = $checkIn->diffInDays($checkOut) - $booking->extend_days;

        $this->nights_amount= ($this->nights * $roomtype->price) * $booking->room;

        $this->total_nights = $this->nights + $booking->extend_days;

        $this->extend_hours_amount = ($roomtype->price * 0.05) * $booking->extend_hours;
        $this->extend_days_amount = $roomtype->price * $booking->extend_days;

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

        if($dining != null){

            $items = OrderItem::whereIn('order_transaction_id', $order_id)->get();

            $total = 0;
            foreach ($items as $item) {

                $this->dining_data[] = [
                    'order' => $item->order,
                    'order_id' => $item->order_transacton_id,
                    'product_name' => $item->product->product_name,
                    'price' => $item->product->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total_price,
                    'date_created' => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                ];

                $total += $item->total_price;
            }

            $this->dine_sub_total = $total;
        }


        $this->advance_payment = $this->payments->paid_amount;

        $this->room_price = $booking->room * $roomtype->price;

        $this->remaining_amount = $this->book_sub_total + (int)$this->dine_sub_total - $this->advance_payment;

        $this->total_amount = $this->book_sub_total + (int)$this->dine_sub_total;

        $this->dispatch('show-guest-folio-modal');
        
    }

    public function close(){

        $this->dispatch('close-guest-folio-modal');

        $this->reset();
    }
}
