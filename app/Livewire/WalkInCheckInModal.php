<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Guest;
use Livewire\Component;
use App\Models\Roomtype;
use App\Models\RoomBooking;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\BookingChildAge;
use App\Models\BookingTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class WalkInCheckInModal extends Component
{
    public function render()
    {
        return view('livewire.manage-check-in-and-out.modals.walk-in-check-in-modal');
    }

    public $firstname;
    public $lastname;
    public $contact_no;
    public $email;
    public $advance_payment;
    public $check_in;
    public $check_out;
    public $adult_no = 1;
    public $children_no = 0;
    public $extra_bed = 0;
    public $room_no = 0;

    public $child_ages = [];
    public $room_id = [];

    public $availableRooms = [];
    public $roomtype_fetch_id;
    public $roomtype_id;
    public $image;

    public $nights = 0;
    public $roomPrice = 0;
    public $extraBedPrice;
    public $room_capacity = 0;

    public $total_guest;
    public $roomtypes;
    
    #[On('view-check-in-modal')]
    public function bookingModal($id){
        
        $user = Auth::user();

        $this->roomtypes = Roomtype::where('user_id', $user->id)
        ->where('id', $id)
        ->where('remove_status', false)
        ->first();

        $rooms = Room::where('user_id', $user->id)
        ->where('roomtype_id', $id)
        ->where('remove_status', false)
        ->where(function ($query) {
            $query->where('status', true);
        })->get();

        $this->roomtype_fetch_id = $this->roomtypes->id;
        $this->roomtype_id = $this->roomtypes->roomtype;
        $this->image = $this->roomtypes->image;
        $this->availableRooms = $rooms;

        $today = today()->format('Y-m-d');

        $this->check_in = $today;
        
        $this->updatedTotalCapacity();

        $this->dispatch('show-booking-modal');
    }

    public function close()
    {
        $this->dispatch('close-booking-modal');
        $this->reset();
        $this->resetValidation();
    }


    public function updateAvailableRooms()
{

    $user = Auth::user();

    $booking = BookingTransaction::where('user_id', $user->id)->get();

    if (!empty($this->check_in) && !empty($this->check_out)) {

        $rooms = Room::where('user_id', $user->id)
        ->where('roomtype_id', $this->roomtype_fetch_id)
        ->where('remove_status', false)
        ->where('room_status', '!=', 'Reserved')
        ->where('room_status', '!=', 'Block')
        ->where(function ($query) {
            $query->where('status', true)
                ->orWhere(function ($subquery) {
                    $subquery->where('status', false)
                        ->whereNotExists(function ($subquery) {
                            $subquery->selectRaw(1)
                                ->from('room_bookings')
                                ->whereColumn('room_bookings.room_id', 'rooms.id')
                                ->where('cancel_status', false)
                                ->where(function ($q) {
                                    $q->where('check_in', '<', $this->check_out)
                                    ->orWhere('check_in', '==', $this->check_out);
  
                                });
                        });

                })->orWhere(function ($subquery) {
                    $subquery->where('status', false)
                        ->whereNotExists(function ($subquery) {
                            $subquery->selectRaw(1)
                                ->from('room_bookings')
                                ->whereColumn('room_bookings.room_id', 'rooms.id')
                                ->where('cancel_status', false)
                                ->where(function ($q) {
                                    $q->where('check_out', '>', $this->check_in)
                                    ->orWhere('check_out', '==', $this->check_in);
  
                                });
                        });
                });
        })
        ->get();

    $this->resetValidation('room_no');

    $this->recalculate();
    
    $this->availableRooms = $rooms;


}

    
}



    protected $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'contact_no' => 'required|numeric|digits_between:11,11',
        'room_id' => 'required|not_in:0',
        'check_in' => 'required|date',
        'check_out' => 'required|date',
        'email' => 'required|email',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName,[
            'firstname' => 'required',
            'lastname' => 'required',
            'contact_no' => 'required|numeric|digits_between:11,11',
            'room_id' => 'required|not_in:0',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'email' => 'required|email',
            'advance_payment' => 'required|numeric|min:'.$this->roomPrice / 2,
        ]);

        if (!empty($this->extra_bed)){
            if(!empty($this->room_capacity)){
               
            if($this->extra_bed > $this->room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' . $this->room_capacity);
            }
            }
        }



    }

    public function updatedRoomCapacity(){
    
        if(!empty($this->room_capacity)){
            if($this->extra_bed > $this->room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' . $this->room_capacity);
            }
        }   
          
    }

    public $roomCount;

    public function updatedRoomNo($value){

        if($this->room_no > 0){
            $this->room_id = array_fill(1, $value, '');
        }

        if($this->room_no == 0 || $this->room_no == ''){
            $this->room_no = 1;

            $this->reset('room_id','room_capacity');

        }else{
            $this->resetValidation('room_no');
        }

        $this->roomCount = Room::where('user_id', auth()->user()->id)
        ->where('roomtype_id', $this->roomtype_fetch_id)
        ->where('status', true)
        ->where('remove_status', false) 
        ->where('room_status', '!=', 'Reserved')
        ->where('room_status', '!=', 'Block')
        ->count();
    

        if(!empty($this->check_in) && !empty($this->check_out)){

            $this->updateAvailableRooms();
            if($this->room_no > count($this->availableRooms)){

                $this->addError('room_no', 'Number of available rooms is: '.count($this->availableRooms));

            }else{
                $this->resetValidation('room_no');
            }
        }else{
            if($this->room_no > $this->roomCount){

                $this->addError('room_no', 'Number of available rooms is: '.$this->roomCount);

            }else{
                $this->resetValidation('room_no');
            }
        }


        $this->recalculate();

        $this->totalGuest();

        $this->updatedTotalCapacity();

        $this->resetValidation('extra_bed');
    
        $this->updatedRoomCapacity();
    }

    public function updatedAdultNo(){

        $this->totalGuest();

        if($this->adult_no === ''){
            $this->adult_no = 1;

            $this->totalGuest();

        }

        $this->resetValidation('extra_bed');
    
        $this->updatedRoomCapacity();
    }

    public function updatedChildrenNo(){

        // if(!empty($this->children_no)){
        //     $this->child_ages = array_fill(1, $value, '');
        // }
        
       
        $this->totalGuest();
        if($this->children_no === ''){
            $this->children_no = 0;

            // $this->reset('child_ages');
            $this->totalGuest();
            
        }
        $this->resetValidation('extra_bed');
    
        $this->updatedRoomCapacity();
    }

    // public function updatedChildAges(){

    //     $this->totalGuest();
    // }

    public function updatedExtraBed()
{
    
    $this->recalculate();

    $this->totalGuest();

    $this->updatedTotalCapacity();

    $this->resetValidation('extra_bed');
    
    $this->updatedRoomCapacity();
}

private function totalGuest()
{
    if (!empty($this->adult_no) || !empty($this->children_no)) {
        $totalGuests = (int)$this->adult_no;

        foreach ($this->child_ages as $age) {
            // Check if the age is 7 or above, add to total_guest if true
            if (!empty($age) && (int)$age >= 7) {
                $totalGuests++;
            }
        }

        if (!empty($this->extra_bed)) {
            if(!empty($this->room_no)){

                $room_capacity = ((int)$this->roomtypes->capacity * $this->room_no) + (int)$this->extra_bed;

                if ($totalGuests > $room_capacity) {
                    $this->addError('total_guest', 'Number of guests cannot exceed ' . $room_capacity);
                } else {
                    $this->resetValidation('total_guest');
                }
            }else{

                $room_capacity = (int)$this->roomtypes->capacity + (int)$this->extra_bed;

                if ($totalGuests > $room_capacity) {
                    $this->addError('total_guest', 'Number of guests cannot exceed ' . $room_capacity);
                } else {
                    $this->resetValidation('total_guest');
                }
            }
            
           
        } else {

            if(!empty($this->room_no)){
                $roomCapacity = $this->roomtypes->capacity * $this->room_no;

                if ($totalGuests > $roomCapacity) {
                    $this->addError('total_guest', 'Number of guests cannot exceed ' . $roomCapacity);
                } else {
                    $this->resetValidation('total_guest');
                }
            }
            else
            {
                $roomCapacity = $this->roomtypes->capacity;

                if ($totalGuests > $roomCapacity) {
                    $this->addError('total_guest', 'Number of guests cannot exceed ' . $roomCapacity);
                } else {
                    $this->resetValidation('total_guest');
                }
            }

            
        }

        $this->total_guest = $totalGuests;
    } else {
        $this->total_guest = 0;
    }
}

public function updatedCheckIn()
    {
        $this->recalculate();

            $this->updateAvailableRooms();
        
            $this->updatedExtraBed();
    }

    public function updatedCheckOut()
    {
        $this->recalculate();

            $this->updateAvailableRooms();

            if($this->check_in >= $this->check_out){
                $this->addError('check_out', 'Please choose a date that comes after the check-in date.');
            }

            $this->updatedExtraBed();

    }

    private function recalculate()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);

            // Calculate the stay duration based on check-in and check-out dates
            $this->nights = $checkIn->diffInDays($checkOut);

            $total_cost = $this->roomtypes->price * $this->nights;

            if (!empty($this->extra_bed)) {

                $this->extraBedPrice = 600* $this->extra_bed;

                if($this->room_no > 0 || $this->room_no != ''){
                    $this->roomPrice = ($total_cost * $this->room_no) + $this->extraBedPrice;

                    $this->advance_payment = $this->roomPrice / 2;
                }else{
                    $this->roomPrice = $total_cost + $this->extraBedPrice;

                    $this->advance_payment = $this->roomPrice / 2;
                }
            } else {

                $this->extraBedPrice = 0;

                if($this->room_no > 0 || $this->room_no != ''){
                $this->roomPrice = $total_cost * $this->room_no;

                $this->advance_payment = $this->roomPrice / 2;
                }else{
                    $this->roomPrice = $total_cost;

                    $this->advance_payment = $this->roomPrice / 2;
                }
            }   
            
        }else {
            // If either check-in or check-out is empty, reset nights and roomPrice
            $this->nights = 0;
            $this->roomPrice = 0;
        }
    }

    public $total_capacity;

    private function updatedTotalCapacity(){
        if (!empty($this->extra_bed)) {
            if(!empty($this->room_no)){

                $this->total_capacity = ((int)$this->roomtypes->capacity * (int)$this->room_no) + (int)$this->extra_bed ;

            }else{

                $this->total_capacity = (int)$this->roomtypes->capacity + (int)$this->extra_bed;
            }
            
        }else{

            if(!empty($this->room_no)){

                $this->total_capacity = (int)$this->roomtypes->capacity * (int)$this->room_no;

            }else{

                $this->total_capacity = (int)$this->roomtypes->capacity;
            }
        }
    }


    public function updatedRoomId($value, $index)
{

    $this->room_capacity = 0;

    foreach($this->room_id as $index => $value){
        if(!empty($value)){
            $rooms = Room::where('user_id', auth()->user()->id)
            ->where('id', $value)
            ->where('remove_status', false)
            ->get();

            foreach ($rooms as $room) {
                $this->room_capacity += $room->extra_bed;
            }

            
        }
    }

    $selectedRoomIds = collect($this->room_id)->reject(function ($roomId) {
        return $roomId === 0;
    });

    if ($selectedRoomIds->count() !== $selectedRoomIds->unique()->count()) {
        $this->addError("room_id", 'Reminder: Please ensure that the selected room numbers are different!');
    } else {
        $this->resetValidation("room_id");
    }

    $this->resetValidation('extra_bed');
    
    $this->updatedRoomCapacity();
}


public function submit()
    {
        $this->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'contact_no' => 'required|numeric|digits_between:11,11',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'email' => 'required|email',
            'extra_bed' => 'numeric||max:'.$this->room_capacity,
            'advance_payment' => 'required|numeric|min:'.$this->roomPrice / 2,
        ]);
    
        // Fetch room type's price
        $roomType = Roomtype::findOrFail($this->roomtype_fetch_id);
        
        $remaining_price = (int)$this->roomPrice - (int)$this->advance_payment;

        $remaining_price = max($remaining_price, 0);

        if(!empty($this->extra_bed)){
            $extra_bed = $this->extra_bed;
        }else{
            $extra_bed = 0;
        }

        if(!empty($this->extraBedPrice)){
            $extra_bed_price = $this->extraBedPrice;
        }else{
            $extra_bed_price = 0;
        }

        $paymentCount = Payment::whereDate('created_at', now()->format('Y-m-d'))->count();

        $sequentialNumber = $paymentCount + 1;
            
        // Format the folio number
        $invoice_no = now()->format('Ymd') . str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);

        // Create a new payment
        $payment = Payment::create([
            'extra_bed_amount' => $extra_bed_price,
            'extended_amount' => 0,
            'payment_method' => $this->payment_method,
            'change' => $this->change,
            'invoice_no' => $invoice_no,
            'paid_amount' => $this->advance_payment,
        ]);

        // Create a new guest
        $guest = new Guest();
        $guest->firstname = $this->firstname;
        $guest->lastname = $this->lastname;
        $guest->contact_no = $this->contact_no;
        $guest->email = $this->email;
        $guest->save();

        $guestCount = Guest::whereDate('created_at', now()->format('Y-m-d'))->count();

        $sequentialNumber = $guestCount;
            
        // Format the folio number
        $guest_folio = now()->format('Ymd') . str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);

        $booking = BookingTransaction::create([
            'user_id' => auth()->user()->id,
            'guest_id' => $guest->id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'total_amount' => $this->roomPrice,
            'remaining_amount' => $remaining_price,
            'room' => $this->room_no,
            'adult' => $this->adult_no,
            'children' => $this->children_no,
            'extra_bed' => $extra_bed,
            'check_in_status' => true,
            'payment_id' => $payment->id,
            'folio_no' => $guest_folio,
            'room_price' => $roomType->price,
        ]);

        // foreach ($this->child_ages as $age) {
        //     BookingChildAge::create([
        //         'booking_transaction_id' => $booking->id,
        //         'child_ages' => $age,
        //     ]);
        // }

        foreach ($this->room_id as $id) {
            RoomBooking::create([
                'booking_transaction_id' => $booking->id,
                'room_id' => $id,
                'check_in' => $this->check_in,
                'check_out' => $this->check_out,
                'check_in_status' => true,
            ]);

            $room = Room::find($id);
            if ($room) {
                $room->status = false;
                $room->room_status = 'Occupied';
                $room->save();
            }
        }


        $this->reset();

        $this->dispatch('check-in-success');

        $this->dispatch('updateNotification');

        $this->dispatch('close-booking-modal');

        $this->dispatch('close-payment-method-modal');
    }

    public function decrement($value) {
        if ($value == 'bed') {
            $this->extra_bed = max(0, $this->extra_bed - 1);
            $this->updatedExtraBed();
        } elseif ($value == 'room') {
            $this->room_no = max(1, $this->room_no - 1);
            $this->updatedRoomNo($this->room_no);
        } elseif ($value == 'adult') {
            $this->adult_no = max(1, $this->adult_no - 1);
            $this->updatedAdultNo();
        } elseif ($value == 'child') {
            $this->children_no = max(0, $this->children_no - 1);
            $this->updatedChildrenNo();
        }
    }

    public function increment($value){
        if($value == 'bed'){
            $this->extra_bed++;
            $this->updatedExtraBed();
        }elseif($value == 'room'){
            $this->room_no++;
            $this->updatedRoomNo($this->room_no);
        }elseif($value == 'adult'){
            $this->adult_no++;
            $this->updatedAdultNo();
        }elseif($value == 'child'){
            $this->children_no++;
            $this->updatedChildrenNo();
        }
    }

    public $change = 0;

    public function updatedAdvancePayment(){
        
        if((int)$this->advance_payment > $this->roomPrice){
            $this->updateChange();
        }
            
    }

    public function updatedPaymentMethod(){
        $this->updatedAdvancePayment();
    }

    public function updateChange(){
        
        $this->change = (int)$this->advance_payment - (int)$this->roomPrice;
    }

    public $payment_method;

    public function cancel(){

        $this->dispatch('close-payment-method-modal');

        $this->reset('payment_method');

        $this->hide = false;

        $this->show = true;

        $this->dispatch('show-check-in-modal');
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
}
