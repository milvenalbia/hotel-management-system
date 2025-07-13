<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use App\Models\Roomtype;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManageRoomType extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $roomtype;
    public $capacity = 1;
    public $price;
    public $image;
    public $description;

    public $editroomtype;
    public $editcapacity;
    public $editprice;
    public $editimage;
    public $editdescription;
    

    
    #[Url]
    public $search = '';

    protected $rules = [
        'roomtype' => 'required',
        'capacity' => 'required|numeric',
        'price' => 'required|numeric',
        'image' => 'required|image|max:1024', 
        'description' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $validatedData = $this->validate();
        
        if($this->image){
            $validatedData['image'] = $this->image->store('public/photos');
        }

        $user = Auth::user();

        Roomtype::create([
            'user_id' => $user->id,
            'roomtype' => $this->roomtype,
            'capacity' => $this->capacity,
            'price' => $this->price,
            'description' => $this->description,
            'image' => $validatedData['image'],
        ]);

        session()->flash('success', 'Room Type Created Successfully');

        $this->reset();
    }
    
    public function render()
    {
        $user = Auth::user();
        
        if($this->search && strlen($this->search) > 2) {
            $roomtypes = Roomtype::where('user_id', $user->id) // Filter by the logged-in user's ID
            ->where('remove_status', false)
            ->where(function($query) {
                $query->where('roomtype', 'like', '%' . $this->search . '%')
                    ->orWhere('capacity', 'like', '%' . $this->search . '%')
                    ->orWhere('price', 'like', '%' . $this->search . '%');
            })
            ->get();
        }else {

            $roomtypes = Roomtype::where('user_id', $user->id) // Filter by the logged-in user's ID
            ->where('remove_status', false)->paginate(10);
        }

        foreach ($roomtypes as $roomtype) {
            $roomtype->formatted_price = number_format($roomtype->price, 2, '.', ',');
        }

        return view('livewire.room-management.manage-room-type', compact('roomtypes'));
    }

    // Update Modal

    public $roomTypes_edit_id;
    public $oldImage;

    public function editRoomtype($id)
    {
        $roomTypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->where('remove_status', false)
        ->first();

        $this->roomTypes_edit_id = $roomTypes->id;
        $this->editroomtype = $roomTypes->roomtype;
        $this->editcapacity = $roomTypes->capacity;
        $this->editprice = $roomTypes->price;
        $this->editdescription = $roomTypes->description;
        $this->oldImage = $roomTypes->image;

        $this->dispatch('show-edit-modal');
    }
    
    public function editRoomtypeData()
    {
        //on form submit validation
        $this->validate([
            'editroomtype' => 'required',
            'editcapacity' => 'required|numeric',
            'editprice' => 'required|numeric',
            'editimage' => 'max:1024', 
            'editdescription' => 'required',
        ]);

        $roomTypes = Roomtype::findOrFail($this->roomTypes_edit_id);
        $photo = $roomTypes->image;
            if($this->editimage)
            {
                Storage::delete($roomTypes->image);
                $photo = $this->editimage->store('public/photos');
            }else{
                $photo = $roomTypes->image;
            }
 
            $roomTypes->update([
                'roomtype' => $this->editroomtype,
                'capacity' => $this->editcapacity,
                'price' => $this->editprice,
                'description' => $this->editdescription,
                'image' => $photo,
            ]);
            $this->roomTypes_edit_id='';

        session()->flash('success', 'Room Type has been updated successfully');

        $this->reset();

        $this->dispatch('close-modal');
    }

    public function close()
    {
        $this->reset();
    }

    // Delete Confirmation Modal

    public $roomtype_id;
    public $deleted_roomtypes;

    public function deleteConfirmation($id)
    {
        $this->roomtype_id = $id;

        $roomtypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->first();

        $this->deleted_roomtypes = $roomtypes->roomtype;

        $rooms = Room::where('user_id', auth()->user()->id)
        ->where(function ($query) {
            $query->where('roomtype_id', $this->roomtype_id)
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

        }else{

        $this->dispatch('show-delete-confirmation-modal');

        }
    }

    public function deleteData()
    {


            Room::where('user_id', auth()->user()->id)
            ->where('roomtype_id', $this->roomtype_id)
            ->update(['remove_status' => true]);
    
            $roomTypes = Roomtype::where('user_id', auth()->user()->id)
            ->where('id', $this->roomtype_id)->first();
            
            $roomTypes->update([
                'remove_status' => true,
            ]);
        

        session()->flash('success', 'Roomtype has been removed successfully');

        $this->dispatch('close-modal');

        $this->roomtype_id = '';
    }

    public function cancel()
    {
        $this->roomtype_id = '';
    }

    public function decrement() {

        $this->capacity = max(1, $this->capacity - 1);
    }

    public function increment(){

            $this->capacity++;
    
    }

    public function editDecrement() {

        $this->editcapacity = max(1, $this->editcapacity - 1);
    }

    public function editIncrement(){

        $this->editcapacity++;

    }
}
