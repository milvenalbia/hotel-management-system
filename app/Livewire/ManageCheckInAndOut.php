<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;

class ManageCheckInAndOut extends Component
{

    use WithPagination;

    public $perPage = 10;
    public $today;

    #[Url] 
    public $filter = '';

    #[Url] 
    public $search = '';

    public $formatted_check_in;
    public $formatted_check_out;
    public $check_out_with_time;

    public function render()
    {

        $this->today = today()->format('Y-m-d');

        $query = BookingTransaction::query()
        ->where('user_id', auth()->user()->id)
        ->where('check_out_status', false)
        ->where('cancel_status', false);

        if($this->filter == 'pending'){
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('check_in', '!=', $this->today);
        }
        elseif($this->filter == 'today'){
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('check_in', $this->today);
        }
        elseif($this->filter == 'checked in'){
            $query->where('check_in_status', true);
        }
        elseif($this->filter == 'check out'){
            $query->where('check_in_status', true)
            ->where('check_out_status', false)
            ->where('check_out', $this->today);
        }

        if($this->search && strlen($this->search) > 2) {
            $booking = $query->where(function ($query) {
                $query->whereHas('guest', function ($typeQuery) {
                            $typeQuery->where('firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('lastname', 'like', '%' . $this->search . '%')
                            ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                        });
            })
            ->with('guest')
            ->get();
            
        }else {
            $booking = $query->with('guest')
            ->paginate($this->perPage);
        }

        $formatted_check_ins = [];
        $formatted_check_outs = [];
        $check_outs_with_times = [];

        foreach ($booking as $reservation) {
            // Format check-in and check-out dates for each booking
            $formatted_check_ins[] = Carbon::parse($reservation->check_in)->format('M j, Y');
            $formatted_check_outs[] = Carbon::parse($reservation->check_out)->format('M j, Y');

            $checkoutDateTime = Carbon::parse($reservation->check_out)->setTime(12, 0, 0);
            $newCheckoutDateTime = $checkoutDateTime->addHours($reservation->extend_hours);

            $check_outs_with_times[] = $newCheckoutDateTime->format('M j, Y h:i A');
        }

        return view('livewire.manage-check-in-and-out.manage-check-in-and-out', compact('booking', 'check_outs_with_times', 'formatted_check_outs', 'formatted_check_ins'));
    }

    public function mount($byStatus = null)
    {
        $this->search= $byStatus;
    }


    public function openCheckInModal($id){

        $this->dispatch('check-in-modal', id: $id);
    }
    
    #[On('check-in-modal-success')]
    public function successBooking(){
    
        session()->flash('success', 'Guest has been checked in successfully!');
    
    }

    public function openEditCheckInModal($id){

        $this->dispatch('edit-check-in-modal', id: $id);
    }
    
    #[On('edit-check-in-modal-success')]
    public function successEditBooking(){
    
        session()->flash('success', 'Guest check in information has been updated successfully!');
    
    }

    // public $disabledButtons = [];
    // public $id;
    // public function openCheckOutModal($id){

    //     $this->id = $id;
    //     $this->disabledButtons[$id] = true;

    //     $this->dispatch('check-out-modal', id: $id);
    // }
    
    // #[On('check-out-modal-success')]
    // public function successCheckOut(){
    
    //     $this->disabledButtons[$this->id] = false;

    //     session()->flash('success', 'Check-out transaction has been completed successfully!');
    
    // }

    // #[On('close-check-out-modal')]
    // public function closeCheckOut(){
    
    //     $this->disabledButtons[$this->id] = false;
    
    // }

    // public function openOrder($bookId)
    // {
    //     // Redirect to the order page with the book ID
    //     return redirect()->route('manage-order', ['bookId' => $bookId]);
    // }
}
