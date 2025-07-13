<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderItem;
use App\Mail\CheckOutMail;
use App\Models\RoomBooking;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use App\Models\OrderTransaction;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckOutModal extends Component
{
    public function render()
    {
        return view('livewire.manage-check-in-and-out.modals.check-out-modal');
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

    public $guest_name;
    public $firstname;
    
    #[On('check-out-modal')]
    public function viewCheckOut($id){

        $today = Carbon::now();

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        $user = User::where('id', auth()->user()->id)->first();

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $id)->first();

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

        $this->guest_name = $guest->firstname . ' ' . $guest->lastname;

        $this->firstname = $guest->firstname;

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

        $this->booking_data[] = [
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

        $this->room_price = $booking->room * $booking->room_price;

        $this->remaining_amount = $this->book_sub_total + (int)$this->dine_sub_total - $this->advance_payment;

        $this->total_amount = $this->book_sub_total + (int)$this->dine_sub_total;

        $this->payment = $this->remaining_amount;

        $this->paid_amount = $this->advance_payment + $this->payment;

        $this->dispatch('show-check-out-modal');
        
    }

    public function close(){

        $this->reset();

        $this->dispatch('close-guest-booking-modal');

        $this->dispatch('close-check-out-modal');
    }

    public $payment;
    public $change = 0;

    public function updatedPayment(){
        $this->paid_amount = (int)$this->advance_payment + (int)$this->payment;
        
        if((int)$this->payment > $this->remaining_amount){
            $this->updateChange();
        }
            
    }

    public function updateChange(){
        
        $this->change = (int)$this->payment - (int)$this->remaining_amount;
    }

    public $payment_method;
    public $hideButton = false;

    public function updatedPaymentMethod(){

        $this->payment = $this->remaining_amount;

        if($this->payment_method == 'Cash'){
            $this->hideButton = false;
        }else{
            $this->hideButton = true;
        }

    }

    public function cancel(){

        $this->dispatch('close-payment-method-modal');

        $this->reset('payment_method');

        $this->hide = false;

        $this->show = true;

        $this->hideButton = false;

        $this->dispatch('show-check-out-modal');
    }

    public $hide = false;
    public function payWithPaypal(){

        $this->hide = true;
        $this->dispatch('show-paypal-modal');
    }

    public function payWithCard(){

        $this->hide = true;
        $this->dispatch('show-card-modal');
    }

    public $password = 'password';
    public $username = 'Sample@account';
    public $show = true;

    public function loginPaypal(){

        $this->validate([
            'password' => 'required',
            'username' => 'required'
        ]);

        // Perform a case-sensitive check for the username
            if ($this->password === 'password' && $this->username === 'Sample@account') {

                $this->show = false;
                $this->dispatch('show-paypal-payment');
            } else {
                
            $this->addError('password', 'Invalid Credentials!');
            }
            
    }

    public function updated($property){

        if($this->payment_method == 'Cash'){
            $this->validateOnly($property,[
                'payment' => 'required|numeric|min:' .$this->remaining_amount,
            ]);
        }
    }

    public function submit(){

        $this->validate([
            'payment' => 'required|numeric|min:' .$this->remaining_amount,
            'payment_method' => 'required'
        ]);

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
            ->where('id', $this->booking_edit_id)
            ->first();
        
        $booking->update([
            'check_in_status' => false,
            'check_out_status' => true,
            'booking_status' => false
        ]);

        $payment = Payment::find($booking->payment_id);
    
        if($this->payment_method == 'Cash'){
            $payment->update([
                'paid_amount' => $this->payments->paid_amount + $this->payment,
                'payment_method' => $this->payment_method,
                'change' => $this->change
            ]);
        }else{
            $payment->update([
                'paid_amount' => $this->payments->paid_amount + $this->payment,
                'payment_method' => $this->payment_method,
            ]);
        }
        

        if ($booking) {
            $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking

            RoomBooking::where('booking_transaction_id', $this->booking_edit_id)
            ->update([
                'check_in_status' => false,
                'check_out_status' => true,
                'booking_status' => false
            ]);

            $old_room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 

            Room::where('user_id', auth()->user()->id)
            ->whereIn('id', $old_room_ids)
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
            ->update(['status' => true, 'room_status' => 'Vacant Dirty']);
        
        
        }

        $booking->update([
            'user_id' => auth()->user()->id,
            'check_in_status' => false,
            'check_out_status' => true,
            'booking_status' => false,
        ]);

        // Send email if dinig_data is greater than 5
        // if(count($this->dining_data) > 5){

        //     $dining_data = $this->dining_data;
        //     $booking_data = $this->booking_data;
        //     $other_data = [
        //         'today' => $this->date_today,
        //         'invoice_no' => $this->invoice_no,
        //         'nights' => $this->nights,
        //         'nights_amount' => $this->nights_amount,
        //         'total_nights' => $this->total_nights,
        //         'total_nights_amount' => $this->total_nights_amount,
        //         'extend_hours_amount' => $this->extend_hours_amount,
        //         'extend_days_amount' => $this->extend_days_amount,
        //         'check_in' => $this->check_in,
        //         'check_out' => $this->check_out,
        //         'book_sub_total' => $this->book_sub_total,
        //         'dine_sub_total' => $this->dine_sub_total,
        //         'room_price' => $this->room_price,
        //         'total_amount' => $this->total_amount,
        //         'cash_amount' => $this->paid_amount,
        //     ];

        //     $booking = BookingTransaction::where('user_id', auth()->user()->id)
        //     ->where('id', $this->booking_edit_id)->first();

        //     $guest = Guest::find($booking->guest_id);
            
        //     Mail::to($guest->email)->send(new CheckOutMail($dining_data,$booking_data,$other_data));
        // }
        

        $this->dispatch('print-check-out');

        $this->reset();

        $this->dispatch('check-out-modal-success');

        $this->dispatch('close-guest-booking-modal');

        $this->dispatch('close-payment-method-modal');
    }
}
