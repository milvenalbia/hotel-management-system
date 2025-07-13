<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Guest;
use Livewire\Component;
use App\Models\Roomtype;
use App\Models\RoomBooking;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;

use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Redirect;

class ManageGuestBooking extends Component
{

    use WithPagination;


    public $perPage = 10;
    
    public $today;

    #[Url] 
    public $byStatus = ' ';

    #[Url] 
    public $search = '';

    public $formatted_check_in;
    public $formatted_check_out;

    public function render()
    {
        $this->today = today()->format('Y-m-d');

        $query = BookingTransaction::query()
        ->where('user_id', auth()->user()->id)
        ->where('check_out_status', false)
        ->where('cancel_status', false);

        if ($this->byStatus == 'pending') {
            $query->where('check_in_status', false)
            ->where('check_out_status', false);
        }
        elseif ($this->byStatus == 'active') {
            $query->where('check_in_status', true)
            ->where('check_out_status', false);
        }
        elseif($this->byStatus == 'check out'){
            $query->where('check_out_status', false)
            ->where('check_in_status', true)
            ->where('check_out', $this->today);
        }

        if($this->search && strlen($this->search) > 2) {
            $booking = $query->where(function($query) {
                $query->whereHas('guest', function ($typeQuery) {
                    $typeQuery->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->with('guest')
            ->get();

        }else{
            $booking = $query->with('guest')
            ->paginate($this->perPage);
        }
        
    

        $roomtypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->get();
        $rooms =Room::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->get();

        $bookedRooms = Room::where('status', false) // Filter rooms with status = false (booked rooms)
        ->where('remove_status', false)
        ->get();
        $availableRooms = Room::where('status', true) // Filter rooms with status = true (available rooms)
        ->where('remove_status', false)
        ->get();

        $formatted_check_ins = [];
        $formatted_check_outs = [];

        foreach ($booking as $reservation) {
            $formatted_check_ins[] = Carbon::parse($reservation->check_in)->format('M j, Y');
            $formatted_check_outs[] = Carbon::parse($reservation->check_out)->format('M j, Y');
        }
        

        // $this->autoCancelShowModal();
       
        return view('livewire.manage-booking.manage-guest-booking',compact('rooms','roomtypes','booking', 'formatted_check_ins', 'formatted_check_outs'),);
    }

    public function openModal($id){

        $this->dispatch('view-modal', id: $id);
    }
    
    #[On('update-booking-success')]
    public function successBooking(){
    
        session()->flash('success', 'Reservation has been updated successfully!');
    
    }


    public $disabled = [];
    public $Id;
    public function viewModal($Id){

        $this->Id = $Id;
        $this->disabled[$Id] = true;

        $this->dispatch('view-detail-modal', bookId: $Id);
    }

    #[On('closing-view-guest-modal')]
    public function closeViewModal(){
    
        $this->disabled[$this->Id] = false;
    
    }

    public function deleteModal($id){

        $this->dispatch('delete-modal', Id: $id);
    }

    #[On('delete-success')]
    public function successDelete(){
    
        session()->flash('success', 'Reservation has been cancelled successfully!');
    
    }

    public $disabledButtons = [];
    public $id;
    public function openCheckOutModal($id){

        $this->id = $id;
        $this->disabledButtons[$id] = true;

        $this->dispatch('check-out-modal', id: $id);
    }

    #[On('check-out-modal-success')]
    public function successCheckOut(){
    
        $this->disabledButtons[$this->id] = false;

        session()->flash('success', 'Check-out transaction has been completed successfully!');
    
    }

    #[On('close-check-out-modal')]
    public function closeCheckOut(){
    
        $this->disabledButtons[$this->id] = false;
    
    }

    // public function autoCancelShowModal(){

    //     // Get the current date and time
    //      $now = Carbon::now();
 
    //      // Set the check-in time (2:00 PM)
    //      $checkInTime = Carbon::createFromTime(14, 0, 0); // 14:00:00
 
    //      // Set the check-out time (12:00 PM next day)
    //      $checkOutTime = Carbon::createFromTime(12, 0, 0)->addDay(); // 12:00:00 next day
 
    //      // Get bookings that haven't checked in by the specific time until check-out time
    //      $bookingsToDelete = BookingTransaction::where(function ($q) use ($checkOutTime) {
    //          $q->where('check_in', '<', $checkOutTime);
    //      })
    //      ->where('check_in_status', false)
    //      ->where('check_out_status', false)
    //      ->where('cancel_status', false)
    //      ->where('booking_status', true)
    //      ->get();

    //      if($bookingsToDelete->count() > 0){

    //          $this->dispatch('show-auto-delete-modal');
    //      }
             
 
    //  }

    // public function cancelReservation(){
    //     // Get the current date and time
    //     $now = Carbon::now();
 
    //     // Set the check-in time (2:00 PM)
    //     $checkInTime = Carbon::createFromTime(14, 0, 0); // 14:00:00

    //     // Set the check-out time (12:00 PM next day)
    //     $checkOutTime = Carbon::createFromTime(12, 0, 0)->addDay(); // 12:00:00 next day

    //     // Combine the current date with check-in and check-out times
    //     $checkInDateTime = $now->copy()->setTime($checkInTime->hour, $checkInTime->minute, $checkInTime->second);
    //     $checkOutDateTime = $now->copy()->setTime($checkOutTime->hour, $checkOutTime->minute, $checkOutTime->second);

        
    //     // Get bookings that haven't checked in by the specific time until check-out time
    //     $bookingsToDelete = BookingTransaction::where(function ($q) use ($checkInDateTime ) {
    //         $q->where('check_in', '<', $checkInDateTime );
    //     })
    //     ->where('check_in_status', false)
    //      ->where('check_out_status', false)
    //      ->where('cancel_status', false)
    //      ->where('booking_status', true)
    //     ->get();


    //     foreach($bookingsToDelete as $delete){
    //         $roomBook = RoomBooking::where('booking_transaction_id', $delete->id);

    //         $roomBooking = RoomBooking::where('booking_transaction_id', $delete->id)
    //         ->get();

    //         foreach($roomBooking as $room_id){
    //             $room_ids[] = $room_id->room_id;
    //         }
            
    //     }

    //     $roomBook->update([
    //         'check_in_status' => false,
    //         'check_out_status' => false,
    //         'booking_status' => false,
    //         'cancel_status' => true
    //     ]);
        

    //         Room::where('user_id', auth()->user()->id)
    //         ->whereIn('id', $room_ids)
    //         ->whereNotExists(function ($subquery) {
    //             $subquery->selectRaw(1)
    //                 ->from('room_bookings')
    //                 ->whereColumn('room_bookings.room_id', 'rooms.id')
    //                 ->where(function ($q) {
    //                     $q->where(function ($qq) {
    //                         $qq->where('check_in_status', true)
    //                             ->orWhere('booking_status', true);
    //                     });
    //                 });
    //         })
    //         ->update(['status' => true, 'room_status' => 'Vacant Ready']);
        

    //     // if ($bookingsToDelete) {
    //     //     $roomBookings = $bookingsToDelete->roomBooking; // Retrieve all room bookings associated with the booking
            
    //     //     $id = $roomBookings->pluck('room.id');

    //     //     RoomBooking::where('booking_transaction_id', $this->booking_id)
    //     //     ->whereIn('id', $id)
    //     //     ->update([
    //     //         'check_in_status' => false,
    //     //         'check_out_status' => false,
    //     //         'booking_status' => false,
    //     //         'cancel_status' => true
    //     //     ]);

    //     //     RoomBooking::where('booking_transaction_id', $this->booking_id)
    //     //     ->update([
    //     //         'check_in_status' => false,
    //     //         'check_out_status' => false,
    //     //         'booking_status' => false,
    //     //         'cancel_status' => true
    //     //     ]);
            
    //     //     $room_ids = $roomBookings->pluck('room.id'); // Extract room numbers from the room 
            
    //     //     Room::where('user_id', auth()->user()->id)
    //     //     ->whereIn('id', $room_ids)
    //     //     ->whereNotExists(function ($subquery) {
    //     //         $subquery->selectRaw(1)
    //     //             ->from('room_bookings')
    //     //             ->whereColumn('room_bookings.room_id', 'rooms.id')
    //     //             ->where(function ($q) {
    //     //                 $q->where(function ($qq) {
    //     //                     $qq->where('check_in_status', true)
    //     //                         ->orWhere('booking_status', true);
    //     //                 });
    //     //             });
    //     //     })
    //     //     ->update(['status' => true, 'room_status' => 'Vacant Ready']);
    //     // }

    //     foreach($bookingsToDelete as $booking){
    //         $booking->update([
    //             'cancel_status' => true,
    //         ]);
    //     }
        

    //     $this->dispatch('delete-success');

    //     $this->dispatch('updateNotification');

    //     $this->dispatch('close-guest-booking-modal');
    // }

    public $name;
    public $guest_id;

    public function confirmOrderModal($id){
        $this->guest_id = $id;

        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->first();

        $this->name = $booking->guest->firstname. ' ' .$booking->guest->lastname;

        $this->dispatch('show-order-confirmation-modal');
    }

    public function cancelOrder(){
        $this->guest_id = ' ';

        $this->dispatch('close-guest-booking-modal');
    }

    public function goToOrder(){

        $this->dispatch('close-guest-booking-modal');

        return Redirect::to(route('manage-order', ['bookId' => $this->guest_id, '20?2310/0712?178dineInGuest' => '1'])); 
    }
    
}
