<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Payment;
use Livewire\Component;
use App\Models\Roomtype;
use App\Models\RoomBooking;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\BookingChildAge;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Auth;

class CheckInModal extends Component
{
    public function render()
    {
        return view('livewire.manage-check-in-and-out.modals.check-in-modal');
    }

    public $booking_edit_id;

    public $firstname;
    public $lastname;
    public $contact_no;
    public $email;
    public $check_in;
    public $check_out;
    public $adult_no = 0;
    public $children_no = 0;
    public $extra_bed = 0;
    public $room_no;
    public $orig_room_no;
    public $orig_adult_no;
    public $orig_children_no;
    public $orig_extra_bed;

    public $child_ages = [];
    public $room_id = [];

    public $availableRooms = [];
    public $roomtype_fetch_id;
    public $roomtype_id;
    public $image;

    public $orig_nights;
    public $nights;
    public $roomPrice;
    public $extraBedPrice;
    public $room_capacity;
    public $orig_room_capacity;

    public $total_guest;
    public $orig_total_guest;
    public $roomtypes;
    public $roomTypes;
    public $orig_total_capacity;
    public $total_capacity;

    public $advance_payment;

    public $checked_button = false;
    public $guest_name;

    // public $extra_bed_method = 'plus';
    // public $room_no_method = 'plus';
    // public $adult_no_method = 'plus';
    // public $children_no_method = 'plus';

    public function mount(){
        $this->roomTypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->get();
    }

    public function changeRoomTypes(){
        if($this->roomtype_id != '-1'){

            $this->updateAvailableRooms();

            $countRooms = Room::where('user_id', auth()->user()->id)
            ->where('roomtype_id', $this->roomtype_id)
            ->where('status', true)
            ->where('room_status', '!=', 'Reserved')
            ->where('room_status', '!=', 'Block')
            ->count();

            if($countRooms > 0){
                $this->resetValidation('roomtype_id');
                $this->updateAvailableRooms();
            }elseif($this->roomtype_id != $this->roomtype_fetch_id){
                $this->dispatch('show-roomtype-modal');
            }
            
        }
    }

    public function closeConflictModal(){
        $this->dispatch('close-roomtype-modal');

        $this->roomtype_id = $this->roomtype_fetch_id;

        $this->updateAvailableRooms();

        $this->updatedTotalCapacity();
    }

    #[On('check-in-modal')]
    public function bookingModal($id){

        $now = Carbon::now();
        
        $user = Auth::user();

        $booking = BookingTransaction::where('user_id', $user->id)
        ->where('id', $id)
        ->first();

        $roomBooking = $booking->roomBooking->first();
        $room = $roomBooking->room;
        $roomtype = $room->roomtypes;

        $this->booking_edit_id = $booking->id;
        $this->firstname = $booking->guest->firstname;
        $this->lastname = $booking->guest->lastname;
        $this->guest_name = $booking->guest->firstname . ' ' . $booking->guest->lastname;
        $this->contact_no = $booking->guest->contact_no;
        $this->email = $booking->guest->email;
        $this->orig_adult_no = $booking->adult;
        if($booking->children == null){
            $this->orig_children_no = 0;
        }else{
            $this->orig_children_no = $booking->children;
        }
        
        if($booking->extra_bed == null){
            $this->orig_extra_bed = 0;
        }else{
            $this->orig_extra_bed = $booking->extra_bed;
        }
        $this->orig_room_no = $booking->room;
        $this->check_out = $booking->check_out;
        $this->roomPrice = $booking->total_amount;
        
        if ($booking) {
            $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
        
            $old_room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
            
            $rooms = Room::where('user_id', auth()->user()->id)
            ->whereIn('id', $old_room_ids)
            ->where('remove_status', false)
            ->get();

            $this->orig_room_capacity = 0;
            foreach($rooms as $room){
                $this->orig_room_capacity += $room->extra_bed;
            }
        
        }

        $checkIn = Carbon::parse($booking->check_in);
        $checkOut = Carbon::parse($booking->check_out);

        $this->orig_nights = $checkIn->diffInDays($checkOut);
        
        // $child_ages = BookingChildAge::where('booking_transaction_id', $booking->id)
        // ->where('child_ages', '>', 6)->get();

        // $this->orig_total_guest = $booking->adult + $child_ages->count();
        $this->orig_total_guest = $booking->adult;


        $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('id', $roomtype->id)
        ->where('remove_status', false)
        ->first();
        $this->roomtype_id = $roomtype->id;
        $this->roomtype_fetch_id = $roomtype->id;

        if($now && $booking->check_in = $now){
            $this->check_in = Carbon::parse($now)->format('Y-m-d');

            $this->recalculate();

            $this->advance_payment = $this->roomPrice / 2;

        }elseif($booking->check_in  < $now || $booking->check_in  > $now){
            $this->check_in = Carbon::parse($booking->check_in)->format('Y-m-d');

        }

        if (!empty($booking->extra_bed)) {
            if(!empty($booking->room)){

                $this->orig_total_capacity = ((int)$booking->room * (int)$roomtype->capacity) + (int)$booking->extra_bed;

            }else{

                $this->orig_total_capacity = (int)$roomtype->capacity + (int)$booking->extra_bed;
            }
            
        }else{

            if(!empty($booking->room)){

                $this->orig_total_capacity = ((int)$roomtype->capacity * (int)$booking->room);

            }else{

                $this->orig_total_capacity = (int)$roomtype->capacity;
            }
        }
        
        $this->dispatch('show-check-in-modal');
    }

    public function close()
    {
        if($this->checked_button){
            $booking = BookingTransaction::where('user_id', auth()->user()->id)
            ->where('id', $this->booking_edit_id)
            ->first();

            if ($booking) {
                $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
            
                $room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
                
                Room::whereIn('id', $room_ids)->where('remove_status', false)
                ->update(['status' => false, 'room_status' => 'Occupied']);
            
            }
        }
        $this->dispatch('close-guest-booking-modal');
        $this->reset();
        $this->roomTypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->get();
        $this->resetValidation();
    }

    protected $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'contact_no' => 'required|numeric|digits_between:11,11',
        'room_id' => 'required|not_in:0',
        'check_in' => 'required|date',
        'check_out' => 'required|date',
        'email' => 'required|email',
        'payment_method' => 'required'
    ];

    public function updated($propertyName)
    {
        if($this->checked_button == true || $this->roomtype_id != $this->roomtype_fetch_id){
            if($this->room_no > 0 && $this->children_no < 1){
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'contact_no' => 'required|numeric|digits_between:11,11',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'email' => 'required|email',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'room_id' => 'required',
                    'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                    'payment_method' => 'required'
                ]);
            }elseif($this->room_no < 0 && $this->children_no > 0){
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'contact_no' => 'required|numeric|digits_between:11,11',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'email' => 'required|email',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                    'payment_method' => 'required'
                    
                ]);
            }elseif($this->room_no > 0 && $this->children_no > 0){
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'contact_no' => 'required|numeric|digits_between:11,11',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'email' => 'required|email',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'room_no' => 'required',
                    'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                    'payment_method' => 'required'
                ]);
            }else{
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'contact_no' => 'required|numeric|digits_between:11,11',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'email' => 'required|email',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                    'payment_method' => 'required'
                ]);
            }
            
        }else{
            $this->validateOnly($propertyName,[
                'firstname' => 'required',
                'lastname' => 'required',
                'contact_no' => 'required|numeric|digits_between:11,11',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'email' => 'required|email',
                'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                'payment_method' => 'required'
            ]);
        }
       

        if (!empty($this->extra_bed)){
            if(!empty($this->room_capacity) || !empty($this->orig_room_capacity)){
               
                $extra_bed = (int)$this->extra_bed + (int)$this->orig_extra_bed;

                $room_capacity = (int)$this->room_capacity + (int)$this->orig_room_capacity;

            if($extra_bed > $room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' . $room_capacity);
            }
            }
        }else{
            if(!empty($this->room_capacity) || !empty($this->orig_room_capacity)){
               
                $extra_bed = (int)$this->extra_bed + (int)$this->orig_extra_bed;

                $room_capacity = (int)$this->room_capacity + (int)$this->orig_room_capacity;

            if($extra_bed > $room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' . $room_capacity);
            }
            }
        }



    }

    public function updatedRoomCapacity(){
    
               
        if($this->checked_button || $this->roomtype_id != $this->roomtype_fetch_id){
            if(!empty($this->room_capacity)){
                $extra_bed = $this->extra_bed + (int)$this->orig_extra_bed;

                $room_capacity = $this->room_capacity + (int)$this->orig_room_capacity;
    
                if($extra_bed > $room_capacity){
                    $this->addError('extra_bed', 'Extra bed cannot exceed to: ' .$room_capacity);
                }else{
                    $this->resetValidation('extra_bed');
                } 
            }
        }else{
            $extra_bed = $this->extra_bed + (int)$this->orig_extra_bed;

            $room_capacity = $this->room_capacity + (int)$this->orig_room_capacity;

            if($extra_bed > $room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' .$room_capacity);
            }else{
                $this->resetValidation('extra_bed');
            }
        }  
        
   
}

    public $roomCount;

    public function updatedRoomNo($value){
        
        if($this->room_no > 0){
            $this->room_id = array_fill(1, $value, '');
        } 
        
        if($this->room_no == 0 || $this->room_no == ''){

            $this->reset('room_id','room_capacity');
            

        }else{

            $this->reset('room_capacity');

            $this->resetValidation('room_no');
        }

        $this->roomCount = Room::where('user_id', auth()->user()->id)
        ->where('roomtype_id', $this->roomtype_fetch_id)
        ->where('status', true)
        ->where('remove_status', false)
        ->where('room_status', '!=', 'Block')
        ->where('room_status', '!=', 'Reserved')
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

        $this->resetValidation('extra_bed');

        $this->updatedRoomCapacity();

        $this->updateAvailableRooms();

        $this->recalculate();

        $this->totalGuest();

        $this->updatedTotalCapacity();
    }

    public function updatedExtraBed()
{

    if($this->extra_bed == ''){
        $this->extra_bed = 0;
    }

    $this->resetValidation('extra_bed');

    $this->updatedRoomCapacity();
   
    $this->recalculate();

    $this->updatedTotalCapacity();

    $this->totalGuest();

}

public function updatedAdultNo(){

    $this->totalGuest();

    $this->resetValidation('extra_bed');
    
    $this->updatedRoomCapacity();

}

public function updatedChildrenNo(){

    // if(!empty($this->children_no)){
    //     $this->child_ages = array_fill(1, $value, '');
    // }
    
   
    $this->totalGuest();

    $this->resetValidation('extra_bed');
    
    $this->updatedRoomCapacity();

    // $this->reset('child_ages');
        
}

// public function updatedChildAges(){

//     $this->totalGuest();
// }

private function totalGuest()
{
    if (!empty($this->adult_no) || !empty($this->children_no)) {
        $totalGuests = (int)$this->adult_no;

        // foreach ($this->child_ages as $age) {
        //     // Check if the age is 7 or above, add to total_guest if true
        //     if (!empty($age) && (int)$age >= 7) {
        //         $totalGuests++;
        //     }
        // }

        $totalGuests += (int)$this->orig_total_guest;

        if(!empty($this->total_capacity)){
            $room_capacity = (int)$this->total_capacity;
        }else{

            $room_capacity = (int)$this->orig_total_capacity;
        }

        if ($totalGuests > $room_capacity) {
            $this->addError('total_guest', 'Number of guests cannot exceed ' . $room_capacity);
        } else {
            $this->resetValidation('total_guest');
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
                if($this->orig_extra_bed > 0){
                    $this->extraBedPrice = 600 * ($this->extra_bed + $this->orig_extra_bed);
                }else{
                    $this->extraBedPrice = 600 * (int)$this->extra_bed;
                }
                

                if($this->room_no > 0){

                    if($this->orig_room_no > 0){
                        $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
                    }else{
                        $this->roomPrice = ((int)$this->room_no * $total_cost) + (int)$this->extraBedPrice;
                    }
                }else{

                    if($this->orig_room_no > 0){
                        $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
                    }else{
                        $this->roomPrice = $total_cost + (int)$this->extraBedPrice;
                    }  
                }
            } else {

                if($this->orig_extra_bed > 0){
                    $this->extraBedPrice = 600 * (int)$this->orig_extra_bed;
                }else{
                    $this->extraBedPrice = 600 * (int)$this->extra_bed;
                }

                if($this->room_no > 0){
                    
                    if($this->orig_room_no > 0){
                        $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
                    }else{
                        $this->roomPrice = ($this->room_no * $total_cost) + (int)$this->extraBedPrice;
                    }
                }else{

                    if($this->orig_room_no > 0){
                        $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
                    }else{
                        $this->roomPrice = $total_cost + (int)$this->extraBedPrice;
                    }
                }
            }

            
        }else {
            // If either check-in or check-out is empty, reset nights and roomPrice
            $this->nights = 0;
            $this->roomPrice = 0;
        }
    }

    private function updatedTotalCapacity(){
        if($this->orig_total_capacity > 0){
            if (!empty($this->extra_bed)) {
                if(!empty($this->room_no) && $this->room_no > 0){
    
                    $this->total_capacity = ((int)$this->roomtypes->capacity * (int)$this->room_no) + (int)$this->extra_bed + (int)$this->orig_total_capacity;
    
                }else{
    
                    $this->total_capacity = (int)$this->extra_bed + (int)$this->orig_total_capacity;
                }
                
            }else{
    
                if(!empty($this->room_no) && $this->room_no > 0){
    
                    $this->total_capacity = ((int)$this->roomtypes->capacity * (int)$this->room_no) + (int)$this->orig_total_capacity;
    
                }else{
    
                    $this->total_capacity = (int)$this->orig_total_capacity;
                }
            }
        }else{
            if (!empty($this->extra_bed)) {
                if(!empty($this->room_no) && $this->room_no > 0){
    
                    $this->total_capacity = ((int)$this->roomtypes->capacity * (int)$this->room_no) + (int)$this->extra_bed ;
    
                }else{
    
                    $this->total_capacity = (int)$this->extra_bed;
                }
                
            }else{
    
                if(!empty($this->room_no) && $this->room_no > 0){
    
                    $this->total_capacity = (int)$this->roomtypes->capacity * (int)$this->room_no;
    
                }else{
    
                    $this->total_capacity = 0 ;
                }
            }
        }
        
    }

    public function updateAvailableRooms()
    {
    
        $user = Auth::user();
    
        if (!empty($this->check_in) && !empty($this->check_out)) {
    
            $rooms = Room::where('user_id', $user->id)
            ->where('roomtype_id', $this->roomtype_id)
            ->where('remove_status', false)
            ->where('room_status', '!=', 'Block')
            ->where('room_status', '!=', 'Reserved')
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

            if ($this->roomtype_id == $this->roomtype_fetch_id) {

                $this->availableRooms = $rooms;

                $this->sameRoomtype();
                
                $this->updatedExtraBed();

        
            }else{

                $this->availableRooms = $rooms;

                $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
                ->where('id', $this->roomtype_id)
                ->where('remove_status', false)
                ->first();
        
                $this->orig_adult_no = 0;
        
                $this->orig_extra_bed = 0;
        
                $this->orig_room_no = 0;
        
                $this->orig_children_no = 0;

                $this->orig_total_capacity = 0;

                $this->orig_total_guest = 0;

                $this->orig_room_capacity = 0;
                    
                $this->resetValidation('extra_bed');

                $this->updatedRoomCapacity();

                $this->updatedExtraBed();
                
        
            }

        $this->recalculate(); 
        
        $this->advance_payment = $this->roomPrice /2;
    
    
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

    private function sameRoomtype(){

        if($this->checked_button == false){
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
                ->where('id', $this->booking_edit_id)
                ->first();

                $roomBooking = $booking->roomBooking->first();
                $room = $roomBooking->room;
                $roomtype = $room->roomtypes;

                $this->orig_adult_no = $booking->adult;

                if($booking->children == null){
                    $this->orig_children_no = 0;
                }else{
                    $this->orig_children_no = $booking->children;
                }
                
                if($booking->extra_bed == null){
                    $this->orig_extra_bed = 0;
                }else{
                    $this->orig_extra_bed = $booking->extra_bed;
                }

                if ($booking) {
                    $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
                
                    $old_room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
                    
                    $rooms = Room::where('user_id', auth()->user()->id)
                    ->whereIn('id', $old_room_ids)
                    ->where('remove_status', false)
                    ->get();
        
                    $this->orig_room_capacity = 0;
                    foreach($rooms as $room){
                        $this->orig_room_capacity += $room->extra_bed;
                    }
                
                }

                $this->orig_room_no = $booking->room;
                $this->roomPrice = $booking->total_amount;

                if (!empty($booking->extra_bed)) {
                    if(!empty($booking->room)){
        
                        $this->orig_total_capacity = ((int)$booking->room * (int)$roomtype->capacity) + (int)$booking->extra_bed;
        
                    }else{
        
                        $this->orig_total_capacity = (int)$roomtype->capacity + (int)$booking->extra_bed;
                    }
                    
                }else{
        
                    if(!empty($booking->room)){
        
                        $this->orig_total_capacity = ((int)$roomtype->capacity * (int)$booking->room);
        
                    }else{
        
                        $this->orig_total_capacity = (int)$roomtype->capacity;
                    }
                }

                $this->advance_payment = $this->roomPrice /2;

                // $child_ages = BookingChildAge::where('booking_transaction_id', $booking->id)
                // ->where('child_ages', '>', 6)->get();

                // $this->orig_total_guest = $booking->adult + $child_ages->count();
                $this->orig_total_guest = $booking->adult;

                $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
                ->where('id', $this->roomtype_id)
                ->where('remove_status', false)
                ->first();

            }else{
                $this->orig_adult_no = 0;
        
                $this->orig_extra_bed = 0;
        
                $this->orig_room_no = 0;
        
                $this->orig_children_no = 0;

                $this->orig_total_capacity = 0;

                $this->orig_total_guest = 0;

                $this->orig_room_capacity = 0;

                $this->room_no;

                $this->adult_no;

                $this->extra_bed;

                $this->children_no;

            }

            $this->resetValidation('extra_bed');

            $this->updatedRoomCapacity();

            $this->updatedTotalCapacity();

            $this->totalGuest();

    }

    public function updatedCheckedButton(){

        if($this->roomtype_id == $this->roomtype_fetch_id){
        if($this->checked_button == true){

            $this->orig_adult_no = 0;

            $this->orig_room_capacity = 0;
        
            $this->orig_extra_bed = 0;
    
            $this->orig_room_no = 0;
    
            $this->orig_children_no = 0;

            $this->orig_total_capacity = 0;

            $this->orig_total_guest = 0;

            $this->total_capacity = 0;

            $this->room_no;

            $this->adult_no;

            $this->extra_bed;

            $this->children_no;

            $user = Auth::user();

            $booking = BookingTransaction::where('user_id', $user->id)
            ->where('id', $this->booking_edit_id)
            ->first();

            if ($booking) {
                $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
            
                $room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
                
                Room::where('user_id', auth()->user()->id)
                ->whereIn('id', $room_ids)
                ->where('remove_status', false)
                ->update(['status' => true, 'room_status' => 'Vacant Clean',]);
            
            }

            $this->resetValidation('extra_bed');

            $this->updatedRoomCapacity();

            $this->updatedTotalCapacity();

            $this->totalGuest();

            $this->recalculate();

            $this->advance_payment = $this->roomPrice /2;

        }else{

            $this->total_capacity = 0;

            $this->room_no;

            $this->adult_no;

            $this->extra_bed;

            $this->children_no;

            $user = Auth::user();

            $booking = BookingTransaction::where('user_id', $user->id)
            ->where('id', $this->booking_edit_id)
            ->first();

            $roomBooking = $booking->roomBooking->first();
                $room = $roomBooking->room;
                $roomtype = $room->roomtypes;

                $this->orig_adult_no = $booking->adult;

                if($booking->children == null){
                    $this->orig_children_no = 0;
                }else{
                    $this->orig_children_no = $booking->children;
                }
                
                if($booking->extra_bed == null){
                    $this->orig_extra_bed = 0;
                }else{
                    $this->orig_extra_bed = $booking->extra_bed;
                }

                if ($booking) {
                    $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
                
                    $old_room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
                    
                    $rooms = Room::where('user_id', auth()->user()->id)
                    ->whereIn('id', $old_room_ids)
                    ->where('remove_status', false)
                    ->get();
        
                    $this->orig_room_capacity = 0;
                    foreach($rooms as $room){
                        $this->orig_room_capacity += $room->extra_bed;
                    }
                
                }

                $this->orig_room_no = $booking->room;
                $this->roomPrice = $booking->total_amount;

                // $child_ages = BookingChildAge::where('booking_transaction_id', $booking->id)
                // ->where('child_ages', '>', 6)->get();

                // $this->orig_total_guest = $booking->adult + $child_ages->count();
                $this->orig_total_guest = $booking->adult;

                $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
                ->where('id', $this->roomtype_id)
                ->where('remove_status', false)
                ->first();

                if (!empty($booking->extra_bed)) {
                    if(!empty($booking->room)){
        
                        $this->orig_total_capacity = ((int)$roomtype->capacity * (int)$booking->room) + (int)$booking->extra_bed;
        
                    }else{
        
                        $this->orig_total_capacity = (int)$roomtype->capacity + (int)$booking->extra_bed;
                    }
                    
                }else{
        
                    if(!empty($this->room_no)){
        
                        $this->orig_total_capacity = ((int)$roomtype->capacity * (int)$booking->room);
        
                    }else{
        
                        $this->orig_total_capacity = (int)$roomtype->capacity;
                    }
                }

            if ($booking) {
                $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
            
                $room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
                
                Room::where('user_id', auth()->user()->id)
                ->whereIn('id', $room_ids)
                ->where('remove_status', false)
                ->update(['status' => false, 'room_status' => 'Occupied']);
            
            }

            $this->updatedTotalCapacity();

            $this->totalGuest();

            $this->recalculate();

            $this->resetValidation('extra_bed');

            $this->updatedRoomCapacity();

            $this->advance_payment = $this->roomPrice /2;
        }

    }else{
        $this->checked_button = false;
    }
}

public function submit()
    {
        if($this->checked_button == true || $this->roomtype_id != $this->roomtype_fetch_id){
            $this->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'contact_no' => 'required|numeric|digits_between:11,11',
                'room_id' => 'required|not_in:0',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'email' => 'required|email',
                'adult_no' => 'required|numeric|min:1',
                'room_id' => 'required',
                'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                'payment_method' => 'required'
            ]);
        }else{
            $this->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'contact_no' => 'required|numeric|digits_between:11,11',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'email' => 'required|email',
                'advance_payment' => 'required|numeric|min:' .$this->roomPrice / 2,
                'payment_method' => 'required'
            ]);
        }
    
        // Fetch room type's price
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
            ->where('id', $this->booking_edit_id)
            ->first();

        $remaining_price = (int)$this->roomPrice - (int)$this->advance_payment;

        $remaining_price = max($remaining_price, 0);
        

        // Update payment
        $payment = Payment::find($booking->payment_id);

        $paymentCount = Payment::whereDate('created_at', now()->format('Y-m-d'))->count();

        $sequentialNumber = $paymentCount + 1;
            
        // Format the folio number
        $invoice_no = now()->format('Ymd') . str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);
        
        if(empty($payment)){
            $payment = Payment::create([
                'extra_bed_amount' => $this->extraBedPrice,
                'extended_amount' => 0,
                'paid_amount' => $this->advance_payment,
                'invoice_no' => $invoice_no,
            ]);
        }else{
            $payment->update([
                'extra_bed_amount' => $this->extraBedPrice,
                'extended_amount' => 0,
                'paid_amount' => $this->advance_payment,
                'invoice_no' => $invoice_no,
                'payment_method' => $this->payment_method,
                'change' => $this->change
            ]);
        }
        
        
        // Update guest
        $guest = Guest::find($booking->guest_id);
        $guest->firstname = $this->firstname;
        $guest->lastname = $this->lastname;
        $guest->contact_no = $this->contact_no;
        $guest->email = $this->email;
        $guest->save();

        // if($this->checked_button == true)

        if($this->checked_button == true || $this->roomtype_id != $this->roomtype_fetch_id){

        // $deleteOldAge = BookingChildAge::where('booking_transaction_id', $this->booking_edit_id)
        // ->get();

        // foreach($deleteOldAge as $oldAge){
        //     $oldAge->delete();
        // }

        if ($booking) {
            $roomBookings = $booking->roomBooking; // Retrieve all room bookings associated with the booking
        
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
            ->update(['status' => true, 'room_status' => 'Vacant Clean']);
            
            $deleteOldRooms = RoomBooking::where('booking_transaction_id', $this->booking_edit_id)
            ->get();

            foreach($deleteOldRooms as $rooms){
                $rooms->delete();
            }
        
        }

        $booking->update([
            'user_id' => auth()->user()->id,
            'guest_id' => $guest->id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'total_amount' => $this->roomPrice,
            'remaining_amount' => $remaining_price,
            'room' => $this->room_no,
            'adult' => $this->adult_no,
            'children' => $this->children_no,
            'extra_bed' => $this->extra_bed,
            'check_in_status' => true,
            'payment_id' => $payment->id,
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

            $new_room = Room::find($id);
            if ($new_room) {
                $new_room->status = false;
                $new_room->room_status = 'Ocuppied';
                $new_room->save();
            }
        }
    }else{


        if(!empty($this->room_no)){
            $room_no = $this->room_no;
        }else{
            $room_no = 0;
        }

        if(!empty($this->adult_no)){
            $adult_no = $this->adult_no;
        }else{
            $adult_no = 0;
        }

        if(!empty($this->children_no)){
            $children_no = $this->children_no;
        }else{
            $children_no = 0;
        }

        if(!empty($this->extra_bed)){
            $extra_bed = $this->extra_bed;
        }else{
            $extra_bed = 0;
        }
        
        $booking->update([
            'user_id' => auth()->user()->id,
            'guest_id' => $guest->id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'total_amount' => $this->roomPrice,
            'remaining_amount' => $remaining_price,
            'room' => $room_no + $this->orig_room_no,
            'adult' => $adult_no + $this->orig_adult_no,
            'children' => $children_no + $this->orig_children_no,
            'extra_bed' => $extra_bed + $this->orig_extra_bed,
            'check_in_status' => true,
            'payment_id' => $payment->id,
        ]);

        // foreach ($this->child_ages as $age) {
        //     BookingChildAge::create([
        //         'booking_transaction_id' => $booking->id,
        //         'child_ages' => $age,
        //     ]);
        // }

        if(!empty($this->room_no) || $this->room_no > 0){
            foreach ($this->room_id as $id) {
                RoomBooking::create([
                    'booking_transaction_id' => $booking->id,
                    'room_id' => $id,
                    'check_in' => $this->check_in,
                    'check_out' => $this->check_out,
                    'check_in_status' => true,
                ]);

                if (!is_array($id)) {
                    $id = [$id];
                }

                Room::where('user_id', auth()->user()->id)
                ->whereIn('id', $id)
                ->where('remove_status', false)
                ->update(['status' => false, 'room_status' => 'Occupied']);
            }

        }else{
                
            RoomBooking::where('booking_transaction_id', $this->booking_edit_id)
            ->update([
                'check_in_status' => true,
                'check_in' => $this->check_in,
                'check_out' => $this->check_out,
            ]);

        }

    }


        $this->reset();

        $this->dispatch('check-in-modal-success');

        $this->dispatch('close-booking-modal');

        $this->dispatch('close-guest-booking-modal');

        $this->dispatch('close-payment-method-modal');
    }

    public function decrement($value) {
        if ($value == 'bed') {
            $this->extra_bed = max(0, $this->extra_bed - 1);
            $this->updatedExtraBed();
        } elseif ($value == 'room') {
            $this->room_no = max(0, $this->room_no - 1);
            $this->updatedRoomNo($this->room_no);
        } elseif ($value == 'adult') {
            $this->adult_no = max(0, $this->adult_no - 1);
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
