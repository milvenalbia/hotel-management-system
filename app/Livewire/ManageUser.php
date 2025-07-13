<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Guest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\BookingTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class ManageUser extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $name;
    public $role = 'Employee';
    public $hotel;
    public $username;
    public $password;
    public $image;
    public $logo;

    public $editname;
    public $editrole;
    public $edithotel;
    public $editusername;
    public $editpassword;
    public $editimage;
    public $editlogo;
    
    public $search = '';
    public function render()
    {
        $current_user = auth()->user()->id;

        if ($this->search && strlen($this->search) > 2) {
            $users = User::where('id', '!=', $current_user)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('role', 'like', '%' . $this->search . '%');
            })
            ->get();
        } else {
            $users = User::where('id', '!=', $current_user)->paginate(10);
        }

        return view('livewire.manage-user', compact('users'));
    }

    protected $rules = [
        'name' => 'required|min:5',
        'role' => 'required',
        'hotel' => 'required|min:5',
        'username' => 'required',
        'password' => 'required',
        'image' => 'required|image|max:1024',
        'logo' => 'required|image|max:1024',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName,[
        'name' => 'required|min:5',
        'role' => 'required',
        'hotel' => 'required|min:5',
        'username' => ['required', Rule::unique('users','username')],
        'password' => 'required',
        'image' => 'required|image|max:1024',
        'logo' => 'required|image|max:1024',
        'editname' => 'required|min:5',
        'editrole' => 'required',
        'edithotel' => 'required|min:5',
        'editusername' => 'required',
        'editimage' => 'max:1024',
        'editlogo' => 'max:1024',
        ]);
    }

    public function submit()
    {
        $validatedData = $this->validate([
            'name' => 'required|min:5',
            'role' => 'required',
            'hotel' => 'required|min:5',
            'username' => ['required', Rule::unique('users','username')],
            'password' => 'required',
            'image' => 'required|image|max:1024',
            'logo' => 'required|image|max:1024',
            ]);

        if($this->image){
            $validatedData['profile_image'] = $this->image->store('public/photos');
        }

        if($this->logo){
            $validatedData['logo'] = $this->logo->store('public/photos');
        }

        User::create([
            'name' => $this->name,
            'role' => $this->role,
            'hotel_name' => $this->hotel,
            'username' => $this->username,
            'password' => $this->password,
            'logo' => $validatedData['logo'],
            'profile_image' => $validatedData['profile_image'],
        ]);

        session()->flash('success', 'User Has Been Created Successfully');

        $this->reset();
    }

    // Update Modal

    public $user_edit_id;
    public $oldImage;
    public $oldlogo;

    public function editUser($id)
    {
        $users = User::where('id', $id)->first();

        $this->user_edit_id = $users->id;
        $this->editname = $users->name;
        $this->editrole = $users->role;
        $this->edithotel = $users->hotel_name;
        $this->editusername = $users->username;
        $this->oldlogo = $users->logo;
        $this->oldImage = $users->profile_image;

        $this->dispatch('show-edit-modal');
    }
    
    public function editUserData()
    {
        //on form submit validation
        $this->validate([
            'editname' => 'required|min:5',
            'editrole' => 'required',
            'edithotel' => 'required|min:5',
            'editusername' => 'required',
            'editimage' => 'max:1024',
            'editlogo' => 'max:1024',
        ]);

        $users = User::findOrFail($this->user_edit_id);
        $image = $users->profile_image;
            if($this->editimage)
            {
                Storage::delete($users->profile_image);
                $image = $this->editimage->store('public/photos');
            }else{
                $image = $users->profile_image;
            }

        $logo = $users->logo;
        if($this->editlogo)
        {
            Storage::delete($users->logo);
            $logo = $this->editlogo->store('public/photos');
        }else{
            $logo = $users->logo;
        }

        if(empty($this->editpassword)){
            $users->update([
                'name' => $this->editname,
                'role' => $this->editrole,
                'hotel_name' => $this->edithotel,
                'username' => $this->editusername,
                'logo' => $logo,
                'profile_image' => $image,

            ]);
        }else{
            $users->update([
                'name' => $this->editname,
                'role' => $this->editrole,
                'hotel_name' => $this->edithotel,
                'username' => $this->editusername,
                'password' => $this->editpassword,
                'logo' => $logo,
                'profile_image' => $image,

            ]);
        }
            $this->user_edit_id='';

        session()->flash('success', 'User has been updated successfully');

        $this->reset();

        $this->dispatch('close-modal');
    }

    public function close()
    {
        $this->reset();

        $this->resetValidation();

        $this->dispatch('close-modal');
    }

    // Delete Confirmation Modal

    public $user_id;

    public $user_name;

    public function deleteConfirmation($id)
    {
        $this->user_id = $id;

        $userAdmin = User::where('role', 'Admin')
        ->get();

        $user = User::where('id', $this->user_id)->first();

        $this->user_name = $user->name;

        if($user->role == 'Admin'){
            if($userAdmin->count() > 1){
                $this->dispatch('show-delete-confirmation-modal');
            }else{
                $this->dispatch('show-prevent-delete-modal');
            }
        }else{
            $this->dispatch('show-delete-confirmation-modal');
        }

        

    }

    public function deleteData()
    {
 
        $users = User::where('id', $this->user_id)->first();

        $bookings = BookingTransaction::where('user_id',$this->user_id)->get();

        $guestIds = $bookings->pluck('guest.id');

        $paymentIds = $bookings->pluck('payment.id');

        Guest::whereIn('id', $guestIds)->delete();

        Payment::whereIn('id', $paymentIds)->delete();

        Storage::delete($users->profile_image);
        Storage::delete($users->logo);
        $users->delete();

        session()->flash('success', 'User has been deleted successfully');

        $this->dispatch('close-modal');

        $this->user_id = '';
    }

    public function cancel()
    {
        $this->user_id = '';
    }

    public function updatedName(){
        if(strtolower($this->name) === $this->name){
            $this->name = ucfirst($this->name);
        }
    }
}
