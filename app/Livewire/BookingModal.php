<?php

namespace App\Livewire;

use App\Mail\Email;
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
use Illuminate\Support\Facades\Mail;

class BookingModal extends Component
{
    public function render()
    {
        return view('livewire.manage-booking.modals.booking-modal');
    }

    public $firstname;
    public $lastname;
    public $contact_no;
    public $email;
    public $check_in;
    public $check_out;
    public $adult_no = 1;
    public $children_no = 0;
    public $extra_bed = 0;
    public $room_no;

    public $child_ages = [];
    public $room_id = [];

    public $availableRooms = [];
    public $roomtype_fetch_id;
    public $roomtype_id;
    public $image;

    public $nights = 0;
    public $roomPrice = 0;
    public $extraBedPrice;
    public $room_capacity;

    public $total_guest;
    public $roomtypes;
    public $rooms;
    
    #[On('open-booking-modal')]
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

    $this->recalculate();

    $this->resetValidation('room_no');
    
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
    
        if(!empty($this->room_capacity) || $this->room_capacity === 0){
            if($this->extra_bed > $this->room_capacity){
                $this->addError('extra_bed', 'Extra bed cannot exceed to: ' .$this->room_capacity);
            }
        }  
          
    }

    public $roomCount;

    public $roomValue = false;

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

    $this->resetValidation('extra_bed');
    
    $this->updatedRoomCapacity();
    
    $this->recalculate();

    $this->totalGuest();

    $this->updatedTotalCapacity();
}

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
                }else{
                    $this->roomPrice = $total_cost + $this->extraBedPrice;
                }
            } else {
                if($this->room_no > 0 || $this->room_no != ''){
                $this->roomPrice = $total_cost * $this->room_no;
                }else{
                    $this->roomPrice = $total_cost;
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
            $this->rooms = Room::where('user_id', auth()->user()->id)
            ->where('id', $value)
            ->where('remove_status', false)
            ->get();

            foreach ($this->rooms as $room) {
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

public $date_today;


public function submit()
    {
        $this->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'contact_no' => 'required|numeric|digits_between:11,11',
            'room_id' => 'required|not_in:0',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'email' => 'required|email',
            'extra_bed' => 'numeric||max:'.$this->room_capacity,
        ]);

    
        // Fetch room type's price
        $roomType = Roomtype::findOrFail($this->roomtype_fetch_id);
        
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

        // Create new payment
        $payment = Payment::create([
            'extra_bed_amount' => $extra_bed_price,
            'extended_amount' => 0,
            'paid_amount' => 0,
            'payment_method' => 'none',
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
            'remaining_amount' => 0,
            'room' => $this->room_no,
            'adult' => $this->adult_no,
            'children' => $this->children_no,
            'extra_bed' => $extra_bed,
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
            ]);

            $room = Room::find($id);
            if ($room) {
                $room->status = false;
                $room->room_status = 'Occupied';
                $room->save();
            }
        }

        // $today = Carbon::now();

        // $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        // if($this->roomPrice > $extra_bed_price){
        //     $total_cost = (int)$this->roomPrice - (int)$extra_bed_price;
        // }else{
        //     $total_cost = (int)$extra_bed_price - (int)$this->roomPrice;
        // }
        

        // // $nights_amount= number_format($total_cost, 2, '.', ',');

        // // $roomtype = $roomType->roomtype;

        // // $r_price = $booking->room_price * $this->room_no;

        // // $room_price = number_format($r_price, 2, '.', ',');

        // // $price = $this->roomPrice + (int)$extra_bed_price;

        // // $total_amount = number_format($price, 2, '.', ',');

        // // $checked_in = Carbon::parse($this->check_in)->format('F j, Y');
        // // $checked_out = Carbon::parse($this->check_out)->format('F j, Y');

        // // $bookingData = [
        // //     'firstname' => $this->firstname,
        // //     'lastname' => $this->lastname,
        // //     'total_amount' => $total_amount,
        // //     'check_in' => $checked_in,
        // //     'check_out' => $checked_out,
        // //     'roomtype' => $roomtype,
        // //     'room' => $this->room_no,
        // //     'stay' => $this->nights,
        // //     'adult' => $this->adult_no,
        // //     'children' => $this->children_no,
        // //     'extra_bed' => $this->extra_bed,
        // //     'today' => $this->date_today,
        // //     'nights_amount' => $nights_amount,
        // //     'phone' => $this->contact_no,
        // //     'email' => $this->email,
        // //     'extra_bed_amount' => (int)$extra_bed_price,
        // //     'room_price' => $room_price,
        // //  ];
        
        // // Mail::to($this->email)->send(new Email($bookingData));

        $this->reset();

        $this->dispatch('booking-success');

        $this->dispatch('close-booking-modal'); 
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

}
