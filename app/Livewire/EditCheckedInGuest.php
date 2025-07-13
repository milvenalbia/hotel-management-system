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

class EditCheckedInGuest extends Component
{
    public function render()
    {
        return view('livewire.manage-check-in-and-out.modals.edit-checked-in-guest');
    }

    public $booking_edit_id;

    public $firstname;
    public $lastname;
    public $check_in;
    public $check_out;
    public $adult_no = 0;
    public $children_no = 0;
    public $extra_bed = 0;
    public $room_no = 0;
    public $orig_room_no;
    public $orig_adult_no;
    public $orig_children_no;
    public $orig_extra_bed;
    public $orig_extend_days;
    public $orig_extend_hours;
    public $orig_check_in;

    public $extend_hours = 0;
    public $extend_days = 0;
    public $extended_amount;

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
    public $room_capacity = 0;
    public $orig_room_capacity;

    public $total_guest;
    public $orig_total_guest;
    public $roomtypes;
    public $roomTypes;
    public $orig_total_capacity;
    public $total_capacity;

    public $checked_button = false;

    public function mount(){
        $this->roomTypes = Roomtype::where('user_id', auth()->user()->id)->get();
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
                $this->dispatch('show-info-modal');
            }
        }
    }

    public function closeInfoModal(){
        $this->dispatch('close-info-modal');

        $this->roomtype_id = $this->roomtype_fetch_id;

        $this->updateAvailableRooms();
    }

    public function updatedExtendHours(){

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $this->booking_edit_id)
        ->first();
        
        if(!empty($booking->extend_hours) && $booking->extend_hours > 0){
            if($this->extend_hours + $booking->extend_hours > 1){
                $this->addError('extend_hours', "You've reached or exceeded the maximum hours allowed");
            }
            else{
                $this->resetValidation('extend_hours');
            }
        }else{
            if($this->extend_hours > 0){
                if($this->extend_hours > 1){
                    $this->addError('extend_hours', 'The hour can only be extended by a maximum of 1 hour.');
                }else{
                    $this->resetValidation('extend_hours');
                }
            }
        }
        

        $this->checkExtendedDay();

        $this->calculateExtendPrice();
    }

    public function updatedExtendDays(){

            $this->checkExtendedDay();

            $this->calculateExtendPrice();

            $this->updatedExtendHours();

    }

    private function calculateExtendPrice(){


        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $this->booking_edit_id)
        ->first();

        $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('id', $this->roomtype_id)->first();

        if($this->roomtype_id == $this->roomtype_fetch_id){
            if (!empty($this->extra_bed) && $this->extra_bed > 0) {
                $this->extraBedPrice = 600 * $this->extra_bed;
                $extraBedPrice = $this->extraBedPrice;
            } else {
                $this->extraBedPrice = 0;
                $extraBedPrice = 0;
            }
        }else{

            if($this->extra_bed > $booking->extra_bed){
                $bed_no = (int)$this->extra_bed - (int)$booking->extra_bed;
            }else{
                $bed_no = 0;
            }
            
            if (!empty($this->extra_bed)) {
                $this->extraBedPrice = 600 * $bed_no;
                $extraBedPrice = $this->extraBedPrice;
            } else {
                $extraBedPrice = 0;
            }
        }
        
        $extend_hour_price = ($this->roomtypes->price * 0.05) * $this->extend_hours;
        if($this->roomtype_id == $this->roomtype_fetch_id && $this->checked_button == false){
            if($this->room_no > 0){
                if($booking->room > 0){
                    $extend_day_price =($this->extend_days * $this->roomtypes->price) * ($booking->room + $this->room_no);
                }else{
                    $extend_day_price = ($this->roomtypes->price * $this->extend_days) * $this->room_no;
                }
            }else{
                if($booking->room > 0){
                    $extend_day_price =($this->extend_days * $this->roomtypes->price) * $booking->room ;
                }else{
                    $extend_day_price = $this->roomtypes->price * $this->extend_days;
                }
            }
        }else{
            if($this->room_no > 0){
                if($this->room_no > $booking->room){
                    $room_no = (int)$this->room_no - (int)$booking->room;
                }else{
                    $room_no = 0;
                }

                    $extend_day_price = ($this->roomtypes->price * $this->extend_days) * $room_no;
            }else{
                    $extend_day_price = $this->roomtypes->price * $this->extend_days;
            }
        }
            
        
        if($this->roomtype_id == $this->roomtype_fetch_id && $this->checked_button == false){
            if($this->room_no > 0){
                if($booking->room > 0){
                    $this->extended_amount = (int) $extend_hour_price + (int) $extend_day_price;
                    $room_price = $this->roomtypes->price * $this->room_no;
                }else
                {
                    $this->extended_amount = (int) $extend_hour_price + (int) $extend_day_price;
                    $room_price = $this->roomtypes->price * $this->room_no;  
                }
            }else{
                $this->extended_amount = (int) $extend_hour_price + (int) $extend_day_price;
                $room_price = 0;
            }
        }else{
            if($this->room_no > 0){

                if($this->room_no > $booking->room){
                    $room_no = (int)$this->room_no - (int)$booking->room;
                }else{
                    $room_no = 0;
                }

                $this->extended_amount = (int) $extend_hour_price + (int) $extend_day_price;
                $room_price = $this->roomtypes->price * (int)$room_no;
            }else{
                $this->extended_amount = (int) $extend_hour_price + (int) $extend_day_price;
                $room_price = 0;
            }
        }
        
        
        $this->roomPrice = $booking->remaining_amount + $extraBedPrice + $this->extended_amount + $room_price;

        if ($this->roomPrice < 0) {
            $this->roomPrice = 0;
        }
    }

    private function checkExtendedDay(){
        // Remember that in livewire order matters, this determine the first thing to do
    
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $this->booking_edit_id)
        ->first();

        $roomBooking = $booking->roomBooking;

        $roomIds = $roomBooking->pluck('room.id');

        $checkoutDateTime = Carbon::parse($booking->check_out)->setTime(12, 0, 0);
        $newCheckoutDateTime = $checkoutDateTime->addHours($this->extend_hours)->addDays($this->extend_days);

        $this->check_out = $newCheckoutDateTime->format('F d, Y h:i A');


        $extended_checkOut = Carbon::parse($this->check_out)->format('Y-m-d');

        if($this->roomtype_id == $this->roomtype_fetch_id && $this->checked_button == false){

            // This will get the value then check if its null
            foreach ($this->room_id as $roomId => $value) {
                if ($value === "") {
                    $verifyRoomNo = roomBooking::whereIn('room_id', $roomIds)
                    ->where('check_in_status', false)
                    ->where('booking_status', true)
                    ->first();
                } else {
                    // This will get the original room id adn set it to array
                    $roomIdsFromModel = $roomBooking->pluck('room.id')->toArray();

                    // This will merged the original room id and the new room_id selected
                    $mergedRoomIds = array_merge((array)$value, $roomIdsFromModel);

                    $verifyRoomNo = roomBooking::whereIn('room_id', $mergedRoomIds)
                        ->where('check_in_status', false)
                        ->where('booking_status', true)
                        ->first();
                }
            }
        }else{
            if(!empty($this->room_id) && count($this->room_id) > 0){
                $verifyRoomNo = roomBooking::whereIn('room_id', $this->room_id)
                ->where('check_in_status', false)
                ->where('booking_status', true)
                ->first();
            }
        }

        
            if($this->room_id != null && count($this->room_id) > 1){
                
            }else{
                $verifyRoomNo = roomBooking::whereIn('room_id', $roomIds)
                ->where('check_in_status', false)
                ->where('booking_status', true)
                ->first();
            }
        
        

        if($this->roomtype_id == $this->roomtype_fetch_id){
            if(!empty($verifyRoomNo->check_in)){

                if($extended_checkOut > $verifyRoomNo->check_in){
                    $this->dispatch('show-conflict-modal');
                }
            } 
        }

        $this->calculateExtendPrice();
            
    }

    public function closeConflictModal(){
        $this->dispatch('close-conflict-modal');

        $this->extend_days = 0;

        $this->checkExtendedDay();

        $this->updatedExtendHours();

        $this->calculateExtendPrice();
    }

    #[On('edit-check-in-modal')]
    public function bookingModal($id){

        $now = Carbon::now();
        
        $user = Auth::user();

        $booking = BookingTransaction::where('user_id', $user->id)
        ->where('id', $id)
        ->first();

        $roomId = $booking->roomBooking;

        $roomBooking = $booking->roomBooking->first();
        $room = $roomBooking->room;
        $roomtype = $room->roomtypes;

        $this->booking_edit_id = $booking->id;
        $this->firstname = $booking->guest->firstname;
        $this->lastname = $booking->guest->lastname;
        $this->orig_adult_no = $booking->adult;
        $this->orig_check_in = $booking->check_in;

        if(!empty($booking->extend_days) && $booking->extend_days > 0){
            $this->orig_extend_days = $booking->extend_days;
        }else{
            $this->orig_extend_days = 0;
        }
        if(!empty($booking->extend_hours) && $booking->extend_hours > 0){
            $this->orig_extend_hours = $booking->extend_hours;
        }else{
            $this->orig_extend_hours = 0;
        }

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
        $this->check_in = Carbon::parse($booking->check_in)->format('F j, Y');
        if(!empty($booking->extend_hours) && $booking->extend_hours > 0){
            $checkoutDateTime = Carbon::parse($booking->check_out)->setTime(11, 0, 0);
            $newCheckoutDateTime = $checkoutDateTime->addHours($booking->extend_hours);

            $this->check_out = $newCheckoutDateTime->format('F d, Y h:i A');
        }else{
            $this->check_out = Carbon::parse($booking->check_out)->format('F j, Y');
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
        
        $this->roomPrice = $booking->remaining_amount;
        
        $this->room_id = $roomId->pluck('room.id');

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
        
        $this->dispatch('show-edit-check-in-modal');
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
                
                Room::whereIn('id', $room_ids)
                ->where('remove_status', false)
                ->update(['status' => false]);
            
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
        'room_id' => 'required|not_in:0',
        'check_in' => 'required|date',
        'check_out' => 'required|date',
        'extend_hours' => 'numeric|max:1'
    ];

    public function updated($propertyName)
    {
        if($this->checked_button == true || $this->roomtype_id != $this->roomtype_fetch_id){
            if($this->room_no > 0 ){
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'room_id' => 'required',
                    'extend_hours' => 'numeric|max:1'
                ]);
            }else{
                $this->validateOnly($propertyName,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'room_no' => 'required|numeric|min:1',
                    'adult_no' => 'required|numeric|min:1',
                    'children_no' => 'nullable',
                    'extra_bed' => 'nullable',
                    'extend_hours' => 'numeric|max:1'
                ]);
            }
            
        }else{
            $this->validateOnly($propertyName,[
                'firstname' => 'required',
                'lastname' => 'required',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'extend_hours' => 'numeric|max:1'
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
            if(!empty($this->room_capacity) || $this->room_capacity == 0){
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
               
        // $extra_bed = $this->extra_bed + (int)$this->orig_extra_bed;

        // $room_capacity = $this->room_capacity + (int)$this->orig_room_capacity;

        // if($extra_bed > $room_capacity){
        //     $this->addError('extra_bed', 'Extra bed cannot exceed to: ' .$room_capacity);
        // }else{
        //     $this->resetValidation('extra_bed');
        // }
        
   
}

    public $roomCount;

    public function updatedRoomNo($value){
        
        if($this->room_no > 0){
            $this->room_id = array_fill(1, $value, '');
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
        
        $this->updateAvailableRooms();

        $this->calculateExtendPrice();

        $this->totalGuest();

        $this->updatedTotalCapacity();

        $this->resetValidation('extra_bed');

        $this->updatedRoomCapacity();
    }

    public function updatedExtraBed()
{

    if($this->extra_bed == ''){
        $this->extra_bed = 0;

        $this->resetValidation('extra_bed');
    }

   
    $this->calculateExtendPrice();

    $this->updatedTotalCapacity();

    $this->updatedExtendHours();

    $this->totalGuest();

    $this->resetValidation('extra_bed');

    $this->updatedRoomCapacity();

}

public function updatedAdultNo(){

    $this->totalGuest();

    $this->updatedExtendHours();

    $this->resetValidation('extra_bed');

    $this->updatedRoomCapacity();

}

public function updatedChildrenNo(){

    // if(!empty($this->children_no)){
    //     $this->child_ages = array_fill(1, $value, '');
    // }
    
   
    $this->totalGuest();

    $this->updatedExtendHours();

    $this->resetValidation('extra_bed');

    $this->updatedRoomCapacity();

    // $this->reset('child_ages');
        
}

// public function updatedChildAges(){

//     $this->totalGuest();

//     $this->updatedExtendHours();
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
        $this->calculateExtendPrice();

            $this->updateAvailableRooms();
        
            $this->updatedExtraBed();
    }

    public function updatedCheckOut()
    {
        $this->calculateExtendPrice();

            $this->updateAvailableRooms();

            if($this->check_in >= $this->check_out){
                $this->addError('check_out', 'Please choose a date that comes after the check-in date.');
            }

            $this->updatedExtraBed();

    }

    // private function recalculate()
    // {

    //     $booking = BookingTransaction::where('user_id', auth()->user()->id)
    //         ->where('id', $this->booking_edit_id)
    //         ->first();

    //     $payment = Payment::find($booking->payment_id);

    //     $paid_amount = $payment->paid_amount;

    //     if ($this->check_in && $this->check_out) {

    //         $checkIn = Carbon::parse($this->check_in);
    //         $checkOut = Carbon::parse($this->check_out);

    //         // Calculate the stay duration based on check-in and check-out dates
    //         $this->nights = $checkIn->diffInDays($checkOut);

    //         $total_cost = $this->roomtypes->price * $this->nights;

    //         if (!empty($this->extra_bed)) {
    //             if($this->orig_extra_bed > 0){
    //                 $this->extraBedPrice = 600 * ($this->extra_bed + $this->orig_extra_bed);
    //             }else{
    //                 $this->extraBedPrice = 600 * (int)$this->extra_bed;
    //             }
                
    //             if($this->room_no > 0){

    //                 if($this->orig_room_no > 0){
    //                     $total_amount = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
    //                 }else{
    //                     $total_amount = ((int)$this->room_no * $total_cost) + (int)$this->extraBedPrice;
    //                 }
    //             }else{

    //                 if($this->orig_room_no > 0){
    //                     $total_amount = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
    //                 }else{
    //                     $total_amount = $total_cost + (int)$this->extraBedPrice;
    //                 }  
    //             }

    //         } else {

    //             if($this->orig_extra_bed > 0){
    //                 $this->extraBedPrice = 600 * (int)$this->orig_extra_bed;
    //             }else{
    //                 $this->extraBedPrice = 600 * (int)$this->extra_bed;
    //             }

    //             if($this->room_no > 0){
                    
    //                 if($this->orig_room_no > 0){
    //                     $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
    //                 }else{
    //                     $this->roomPrice = ($this->room_no * $total_cost) + (int)$this->extraBedPrice;
    //                 }
    //             }else{

    //                 if($this->orig_room_no > 0){
    //                     $this->roomPrice = ((int)$this->orig_room_no + (int)$this->room_no) * (int)$total_cost + (int)$this->extraBedPrice;
    //                 }else{
    //                     $this->roomPrice = $total_cost + (int)$this->extraBedPrice;
    //                 }
    //             }
    //         }
            
    //     }else {
    //         // If either check-in or check-out is empty, reset nights and roomPrice
    //         $this->nights = 0;
    //         $this->roomPrice = 0;
    //     }
    // }

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

        $check_out = Carbon::parse($this->check_out)->format('Y-m-d');
    
        $user = Auth::user();
    
        if (!empty($this->orig_check_in) && !empty($check_out)) {
    
            $rooms = Room::where('user_id', $user->id)
            ->where('roomtype_id', $this->roomtype_id)
            ->where('remove_status', false)
            ->where('room_status', '!=', 'Block')
            ->where('room_status', '!=', 'Reserved')
            ->where(function ($query) use ($check_out) {
                $query->where('status', true)
                    ->orWhere(function ($subquery) use ($check_out) {
                        $subquery->where('status', false)
                            ->whereNotExists(function ($subquery) use ($check_out) {
                                $subquery->selectRaw(1)
                                    ->from('room_bookings')
                                    ->whereColumn('room_bookings.room_id', 'rooms.id')
                                    ->where('cancel_status', false)
                                    ->where(function ($q) use ($check_out) {
                                        $q->where('check_in', '<', $check_out)
                                        ->orWhere('check_in', '==', $check_out);
      
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
                                        $q->where('check_out', '>', $this->orig_check_in)
                                        ->orWhere('check_out', '==', $this->orig_check_in);
      
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

                $this->adult_no;
        
                $this->extra_bed;
        
                $this->room_no;
        
                $this->children_no;

                $this->orig_total_capacity = 0;

                $this->orig_total_guest = 0;

                $this->orig_room_capacity = 0;

                $this->resetValidation('extra_bed');

                $this->updatedRoomCapacity();
        
            }
    
            $this->updatedExtendHours();
    
            $this->calculateExtendPrice(); 
    
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

    $this->updatedExtendHours();

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

                // $child_ages = BookingChildAge::where('booking_transaction_id', $booking->id)
                // ->where('child_ages', '>', 6)->get();

                // $this->orig_total_guest = $booking->adult + $child_ages->count();
                $this->orig_total_guest = $booking->adult;

                $this->roomtypes = Roomtype::where('user_id', auth()->user()->id)
                ->where('id', $this->roomtype_id)
                ->where('remove_status', false)
                ->first();

                // if (!empty($booking->extra_bed)) {
                //     if(!empty($booking->room)){
        
                //         $this->orig_total_capacity = ((int)$this->roomtypes->capacity * (int)$booking->room) + (int)$booking->extra_bed;
        
                //     }else{
        
                //         $this->orig_total_capacity = (int)$this->roomtypes->capacity + (int)$booking->extra_bed;
                //     }
                    
                // }else{
        
                //     if(!empty($this->room_no)){
        
                //         $this->orig_total_capacity = ((int)$this->roomtypes->capacity * (int)$booking->room);
        
                //     }else{
        
                //         $this->orig_total_capacity = (int)$this->roomtypes->capacity;
                //     }
                // }

                $this->updatedTotalCapacity();

                $this->calculateExtendPrice();

                $this->updatedExtraBed();
            }else{
                $this->orig_adult_no = 0;
        
                $this->orig_extra_bed = 0;
        
                $this->orig_room_no = 0;
        
                $this->orig_children_no = 0;

                $this->orig_total_capacity = 0;

                $this->orig_total_guest = 0;

                $this->room_no;

                $this->adult_no;

                $this->extra_bed;

                $this->children_no;

                $this->orig_room_capacity = 0;

            }

            $this->calculateExtendPrice();

            $this->updatedExtendHours();

            $this->updatedExtraBed();

    }

    public function updatedCheckedButton(){

        if($this->roomtype_id == $this->roomtype_fetch_id){
        if($this->checked_button == true){

            $this->orig_adult_no = 0;
        
            $this->orig_extra_bed = 0;
    
            $this->orig_room_no = 0;
    
            $this->orig_children_no = 0;

            $this->orig_total_capacity = 0;

            $this->orig_total_guest = 0;

            $this->orig_room_capacity = 0;

            $this->room_id = [];

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
                ->update(['status' => true]);
            
            }

            $this->totalGuest();

            $this->updatedExtraBed();

            $this->calculateExtendPrice();


        }else{

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
                ->update(['status' => false]);
            
            }

            $this->updatedExtraBed();

            $this->calculateExtendPrice();

        }

    }else{
        $this->checked_button = false;
    }

    $this->updatedExtendHours();
}

public function submit()
    {
        if($this->checked_button == true || $this->roomtype_id != $this->roomtype_fetch_id){
            $this->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'room_id' => 'required|not_in:0',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'adult_no' => 'required|numeric|min:1',
                'room_id' => 'required',

            ]);
        }else{
            $this->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
            ]);
        }
    
        // Fetch room type's price
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
            ->where('id', $this->booking_edit_id)
            ->first();

        if($this->roomtype_id == $this->roomtype_fetch_id){
            if (!empty($this->extra_bed)) {
                $this->extraBedPrice = 600 * $this->extra_bed;
                $extraBedPrice = $this->extraBedPrice;
            } else {
                $extraBedPrice = 0;
            }
        }else{

            if($this->extra_bed > $booking->extra_bed){
                $bed_no = (int)$this->extra_bed - (int)$booking->extra_bed;
            }else{
                $bed_no = 0;
            }
            
            if (!empty($this->extra_bed)) {
                $this->extraBedPrice = 600 * $bed_no;
                $extraBedPrice = $this->extraBedPrice;
            } else {
                $extraBedPrice = 0;
            }
        }

        $check_in = Carbon::parse($booking->check_in)->format('Y-m-d');
        $check_out = Carbon::parse($this->check_out)->format('Y-m-d');
        
        $payment = Payment::find($booking->payment_id);

        if(!empty($this->extended_amount)){
            $payment->update([
                'extra_bed_amount' => $extraBedPrice + $payment->extra_bed_amount,
                'extended_amount' => $this->extended_amount,
            ]);
        }
       

        // Create a new guest
        $guest = Guest::find($booking->guest_id);
        $guest->firstname = $this->firstname;
        $guest->lastname = $this->lastname;
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
            ->update(['status' => true, 'room_status' => 'Vacant Ready']);
            
            $deleteOldRooms = RoomBooking::where('booking_transaction_id', $this->booking_edit_id)
            ->get();

            foreach($deleteOldRooms as $rooms){
                $rooms->delete();
            }
        
        }

        $booking->update([
            'user_id' => auth()->user()->id,
            'guest_id' => $guest->id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'remaining_amount' => $this->roomPrice,
            'room' => $this->room_no,
            'adult' => $this->adult_no,
            'children' => $this->children_no,
            'extra_bed' => $this->extra_bed,
            'check_in_status' => true,
            'extend_hours' => $this->extend_hours,
            'extend_days' => $this->extend_days,
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
                'check_in' => $check_in,
                'check_out' => $check_out,
                'check_in_status' => true,
            ]);

            $new_room = Room::find($id);
            if ($new_room) {
                $new_room->status = false;
                $new_room->room_status = 'Occupied';
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
            'check_in' => $check_in,
            'check_out' => $check_out,
            'remaining_amount' => $this->roomPrice,
            'room' => $room_no + $this->orig_room_no,
            'adult' => $adult_no + $this->orig_adult_no,
            'children' => $children_no + $this->orig_children_no,
            'extra_bed' => $extra_bed + $this->orig_extra_bed,
            'check_in_status' => true,
            'extend_hours' => $this->extend_hours + $this->orig_extend_hours,
            'extend_days' => $this->extend_days + $this->orig_extend_days,
        ]);

        // foreach ($this->child_ages as $age) {
        //     BookingChildAge::create([
        //         'booking_transaction_id' => $booking->id,
        //         'child_ages' => $age,
        //     ]);
        // }
        
        if($this->room_no > 0){
        foreach ($this->room_id as $id) {
            RoomBooking::create([
                'booking_transaction_id' => $booking->id,
                'room_id' => $id,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'check_in_status' => true,
            ]);

            if (!is_array($id)) {
                $id = [$id];
            }

            Room::where('user_id', auth()->user()->id)
            ->whereIn('id', $id)
            ->where('remove_status', false)
            ->update(['status' => false, 'room_status' => 'Occupied',]);
        }
    }else{
        
        $updateOldRooms = RoomBooking::where('booking_transaction_id', $this->booking_edit_id)
        ->get();

        foreach($updateOldRooms as $rooms){
            
            $rooms->update([
                'check_in' => $check_in,
                'check_out' => $check_out,
            ]);
        }
    }
    
    }


        $this->reset();

        $this->dispatch('check-in-modal-success');

        $this->dispatch('close-guest-booking-modal');
    }

    public function decrement($value) {
        if ($value == 'bed') {
            $this->extra_bed = max(0, $this->extra_bed - 1);
            $this->updatedExtraBed();
        } elseif ($value == 'room') {
            if($this->checked_button || $this->roomtype_id != $this->roomtype_fetch_id){
                $this->room_no = max(1, $this->room_no - 1);
                $this->updatedRoomNo($this->room_no);
            }else{
                $this->room_no = max(0, $this->room_no - 1);
                $this->updatedRoomNo($this->room_no);
            }
        } elseif ($value == 'adult') {
            if($this->checked_button || $this->roomtype_id != $this->roomtype_fetch_id){
                $this->adult_no = max(1, $this->adult_no - 1);
                $this->updatedAdultNo();
            }else{
                $this->adult_no = max(0, $this->adult_no - 1);
                $this->updatedAdultNo();
            }
            
        } elseif ($value == 'child') {
            $this->children_no = max(0, $this->children_no - 1);
            $this->updatedChildrenNo();

        }elseif ($value == 'hours') {
            $this->extend_hours = max(0, $this->extend_hours - 1);
            $this->updatedExtendHours();

        }elseif ($value == 'days') {
            $this->extend_days= max(0, $this->extend_days - 1);
            $this->updatedExtendDays();
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
        }elseif ($value == 'hours') {
            $this->extend_hours++;
            $this->updatedExtendHours();

        }elseif ($value == 'days') {
            $this->extend_days++;
            $this->updatedExtendDays();
        }
    }
}
