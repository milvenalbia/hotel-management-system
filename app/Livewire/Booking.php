<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use App\Models\Roomtype;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Auth;

class Booking extends Component
{

    use WithPagination;

    #[Url] 
    public $by_status = '';

    #[Url]
    public $guest_search;

    #[Url]
    public $search;

    public $formatted_check_in;
    public $formatted_check_out;

    public function render()
    {
        $user = Auth::user();
        
        if($this->search && strlen($this->search) > 2){

            $roomtypes = Roomtype::where('user_id', $user->id) // Filter by the logged-in user's ID
            ->where('remove_status', false)
            ->where(function($query) {
                $query->where('roomtype', 'like', '%' . $this->search . '%')
                    ->orWhere('capacity', 'like', '%' . $this->search . '%')
                    ->orWhere('price', 'like', '%' . $this->search . '%');
            })
            ->paginate(8);


        }else{

            $roomtypes = Roomtype::where('user_id', $user->id) // Filter by the logged-in user's ID
            ->where('remove_status', false)->paginate(8);
        }
        

        foreach ($roomtypes as $roomtype) {
            
            $roomtype->available_rooms = $this->getAvailableRoomsCount($roomtype->id);
        }

        $this->today = today()->format('Y-m-d');

        $query = BookingTransaction::query()
        ->where('user_id', auth()->user()->id)
        ->where('check_out_status', false)
        ->where('check_in_status', false)
        ->where('cancel_status', false);

        if ($this->by_status == 'pending') {
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->where('check_in', '!=', $this->today);
        }
        elseif($this->by_status == 'arrival'){
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->where('check_in', $this->today);
        }

        if($this->guest_search && strlen($this->guest_search) > 2){
            $booking = $query->where(function($query) {
                $query->whereHas('guest', function ($typeQuery) {
                    $typeQuery->where('firstname', 'like', '%' . $this->guest_search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->guest_search . '%')
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->guest_search . '%']);
                });
            })
            ->with('guest')
            ->get();
        }else {
            $booking = $query->with('guest')
            ->paginate($this->perPage);
        }

        $this->formatted_check_in = [];
        $this->formatted_check_out = [];
        foreach ($booking as $reservation) {
            $this->formatted_check_in[] = Carbon::parse($reservation->check_in)->format('M j, Y');
            $this->formatted_check_out[] = Carbon::parse($reservation->check_out)->format('M j, Y');
        }
        
        return view('livewire.manage-booking.booking', compact('roomtypes','booking'));
    }

    public function resetSearch(){
        $this->resetPage();
    }
    

public function getAvailableRoomsCount($roomTypeId)
{

    $user = Auth::user();
    
    return Room::where('user_id', $user->id)
    ->where(function ($query) use ($roomTypeId) {
        $query->where('roomtype_id', $roomTypeId)
        ->where('remove_status', false)
        ->where('room_status', '!=', 'Reserved')
        ->where('room_status', '!=', 'Block')
        ->where('status', true)
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
            });
    })
    ->count();

}

public $today;

public $perPage = 10;

// public function UpdatedByStatus(){

//         $this->today = today()->format('Y-m-d');

//         $query = BookingTransaction::query()
//         ->where('user_id', auth()->user()->id)
//         ->where('check_out_status', false)
//         ->where('check_in_status', false)
//         ->where('cancel_status', false);

//         if ($this->by_status == 'pending') {
//             $query->where('check_in_status', false)
//             ->where('check_out_status', false)
//             ->where('cancel_status', false)
//             ->where('check_in', '!=', $this->today);
//         }
//         elseif($this->by_status == 'arrival'){
//             $query->where('check_in_status', false)
//             ->where('check_out_status', false)
//             ->where('cancel_status', false)
//             ->where('check_in', $this->today);
//         }

//         $this->booking = $query->where(function($query) {
//                 $query->whereHas('guest', function ($typeQuery) {
//                     $typeQuery->where('firstname', 'like', '%' . $this->search . '%')
//                     ->orWhere('lastname', 'like', '%' . $this->search . '%')
//                     ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
//                 });
//             })
//             ->with('guest')
//             ->paginate($this->perPage);

        

//         // foreach ($this->booking as $reservation) {
//         //     $reservation->formatted_check_in = Carbon::parse($reservation->check_in)->format('M j, Y');
//         //     $reservation->formatted_check_out = Carbon::parse($reservation->check_out)->format('M j, Y');
//         // }
// }

public function openBookingModal($id){

    $this->dispatch('open-booking-modal', id: $id);
}

#[On('booking-success')]
public function successReservation(){

    session()->flash('success', 'Guest has been reserved successfully!');

}

public $firstname;

public $lastname;

public $book_id;

public function openConfirmationModal($id){

    $guest = BookingTransaction::where('user_id', auth()->user()->id)
    ->where('id', $id)
    ->first();

    $this->book_id = $id;

    $this->firstname = $guest->guest->firstname;

    $this->lastname = $guest->guest->lastname;

    $this->dispatch('show-confirm-modal');
}
public function cancel(){

    $this->dispatch('close-booking-modal');
    
}

public function openModal($id){

    $this->dispatch('view-modal', id: $id);
}

#[On('update-booking-success')]
public function successBooking(){

    session()->flash('success', 'Booking has been updated successfully!');

}

public function openCheckInModal($id){

    $this->dispatch('close-booking-modal');

    $this->dispatch('check-in-modal', id: $id);
}

#[On('check-in-modal-success')]
public function registerSuccess(){

    $this->dispatch('close-booking-modal');

    return redirect('/checkInGuest')->with('success', 'Guest has been checked in successfully!');

}


}
