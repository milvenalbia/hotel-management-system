<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use App\Models\Roomtype;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ManageRoom extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $room_no;
    public $roomtype_id;
    public $extra_bed = 0;

    public $editroom_no;
    public $editroomtype_id;
    public $editextra_bed;

    public $byRoomtype;

    protected $rules = [
        'room_no' => 'required',
        'roomtype_id' => 'required|not_in:0',
        
    ];

    public function updated($propertyName)
    {
        
        if($this->editroom_no){
            if($this->room_no == $this->editroom_no){
                $this->validateOnly($propertyName,[
                    'room_no' => 'required',
                    'roomtype_id' => 'required|not_in:0',
                ]);
            }else{
                $this->validateOnly($propertyName,[
                    'room_no' => ['required', Rule::unique('rooms','room_no')->where('user_id', auth()->user()->id)],
                    'roomtype_id' => 'required|not_in:0',
                ]);
            }
        }else{
            $this->validateOnly($propertyName,[
                'room_no' => ['required', Rule::unique('rooms','room_no')->where('user_id', auth()->user()->id)],
                'roomtype_id' => 'required|not_in:0',
            ]);
        }
        
        
    }

    public function openCreateRoomModal(){

        $this->dispatch('show-create-room-modal');
    }


    public function submit()
    {
        $this->validate([
            'room_no' => ['required', Rule::unique('rooms','room_no')->where('user_id', auth()->user()->id)],
            'roomtype_id' => 'required|not_in:0',
        ]);

        $user = Auth::user();

        Room::create([
            'user_id' => $user->id,
            'room_no' => $this->room_no,
            'roomtype_id' => $this->roomtype_id,
            'extra_bed' => $this->extra_bed,
            'room_status' => 'Vacant Ready',
        ]);

        session()->flash('success', 'Room Created Successfully');

        $this->reset();

        $this->dispatch('close-room-modal');
    }

    public function mount($status = null){
        
        $this->status = $status;
    }

    public $search = '';

    public $status = '';

    public $floor = '';

    public function render()
    {

        $user = Auth::user();
        
        $query = Room::where('user_id', $user->id)
        ->where('remove_status', false);

        if($this->status){
            $query->where('room_status', $this->status);
        }

        if($this->floor){
            $query->where('room_no', 'like', $this->floor . '%');
        }

        if($this->byRoomtype){
            $query->where('roomtype_id', $this->byRoomtype);
        }
        
        if($this->search && strlen($this->search) > 2){
            $rooms = $query->where(function ($query) {
                $query->where('room_no', 'like', '%' . $this->search . '%')
                      ->orWhereHas('roomtypes', function ($typeQuery) {
                          $typeQuery->where('roomtype', 'like', '%' . $this->search . '%');
                      });
            })
            ->with('roomtypes')
            ->get();
        }else {
            $rooms = $query->with('roomtypes')->paginate(10);
        }

        

        $roomtypes = Roomtype::where('user_id', $user->id)
        ->where('remove_status', false)
        ->get();

        return view('livewire.room-management.manage-room', compact('rooms','roomtypes'), );
    }

    public $room_edit_id;
    public $room_status;

    public function editroom($id)
    {

        $rooms = Room::where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->where('remove_status', false)
        ->first();

        $this->room_edit_id = $rooms->id;
        $this->editroom_no = $rooms->room_no;
        $this->room_no = $rooms->room_no;
        $this->roomtype_id = $rooms->roomtype_id;
        $this->room_status = $rooms->room_status;
        $this->extra_bed = $rooms->extra_bed;

        $this->dispatch('show-edit-room-modal');
    }
    
    public function editRoomData()
    {
        //on form submit validation
        if($this->room_no == $this->editroom_no){
            $this->validate([
                'room_no' => 'required',
                'roomtype_id' => 'required|not_in:0',
            ]);
        }else{
            $this->validate([
                'room_no' => ['required', Rule::unique('rooms','room_no')],
                'roomtype_id' => 'required|not_in:0',
            ]);
        }
        

        $rooms = Room::findOrFail($this->room_edit_id);
 
            $rooms->update([
                'room_no' => $this->room_no,
                'roomtype_id' => $this->roomtype_id,
                'extra_bed' => $this->extra_bed,
                'room_status' => $this->room_status,
            ]);
            $this->room_edit_id='';

        session()->flash('success', 'Room has been updated successfully');

        $this->reset();

        $this->dispatch('close-room-modal');
    }

    public function close()
    {
        $this->dispatch('close-room-modal');

        $this->reset();

        $this->resetValidation();
    }

    public $room_id;

    public function deleteConfirmation($id)
    {
        $this->room_id = $id;

        $rooms = Room::where('user_id', auth()->user()->id)
        ->where(function ($query) {
            $query->where('id', $this->room_id)
            ->where('remove_status', false)
            ->where('status', false)
                ->whereExists(function ($subquery) {
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

        if($rooms > 0){

            $this->dispatch('show-prevent-modal');
        }
        else
        {
            $this->dispatch('show-delete-room-modal');
        }

    }

    public function deleteData()
    {
 
        $rooms = Room::where('user_id', auth()->user()->id)
        ->where('id', $this->room_id)->first();
        
        $rooms->update([
            'remove_status' => true,
        ]);

        session()->flash('success', 'Room has been removed successfully');

        $this->dispatch('close-room-modal');

        $this->room_id = '';
    }

    public function cancel()
    {
        $this->room_id = '';
    }

    
    public function openHistory($id){

        $this->dispatch('room-guest-history', roomId: $id);
    }

    public function decrement() {

            $this->extra_bed = max(0, $this->extra_bed - 1);
    }

    public function increment(){

            $this->extra_bed++;
       
    }

}
