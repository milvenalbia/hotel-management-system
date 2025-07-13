<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use App\Models\Roomtype;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class WalkInCheckIn extends Component
{
 
    use WithPagination;

    #[Url]
    public $search;

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
            ->get();
        }else{
            $roomtypes = Roomtype::where('user_id', $user->id) // Filter by the logged-in user's ID
            ->where('remove_status', false)->paginate(8);
        }
        

        foreach ($roomtypes as $roomtype) {
            
            $roomtype->available_rooms = $this->getAvailableRoomsCount($roomtype->id);
        }
        
        return view('livewire.manage-check-in-and-out.walk-in-check-in', compact('roomtypes'));
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

public function openModal($id){

    $this->dispatch('view-check-in-modal', id: $id);
}

#[On('check-in-success')]
public function successBooking(){

    session()->flash('success', 'New guest has been checked in successfully!');

}
}
