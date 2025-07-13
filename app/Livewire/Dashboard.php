<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Guest;
use Livewire\Component;
use App\Models\Roomtype;
use Illuminate\Support\Carbon;
use App\Models\OrderTransaction;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Redirect;

class Dashboard extends Component
{
    public $bookings;
    public $guestNameForCheckIn;
    public $guestNameForCheckOut;
    public $roomSales;
    public $productSales;
    public $overallSales;
    public $user_id;
    public $bookingCount;
    public $arrival;
    public $departure;
    public $inactiveCount;
    public $activeCount;
    public $pendingCount;
    public $cancelledCount;

    public function render()
    {

        $today = now()->toDateString();

        $this->user_id = auth()->user()->id;

        $rooms = Room::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->count();

        $availableRooms = Room::where('user_id', auth()->user()->id)
        ->where(function ($query) {
            $query->where('status', true)
                ->where('remove_status', false)
                ->where('room_status', '!=' , 'Block')
                ->where('room_status', '!=' , 'Reserved')
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

        $occupiedRooms = Room::where('user_id', auth()->user()->id)
        ->where(function ($query) {
            $query->where('status', false)
                ->where('remove_status', false)
                ->where('room_status', '!=' , 'Block')
                ->where('room_status', '!=' , 'Reserved')
                ->orWhereExists(function ($subquery) {
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

        $blockRooms = Room::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->where('room_status', 'Block')
        ->count();

        $reservedRooms = Room::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->where('room_status', 'Reserved')
        ->count();

        $this->bookings = BookingTransaction::where('user_id', auth()->user()->id)
        ->whereDate('created_at', $today)
        ->get();

        foreach ($this->bookings as $reservation) {
            $reservation->formatted_check_in = Carbon::parse($reservation->check_in)->format('M j, Y');
            $reservation->formatted_check_out = Carbon::parse($reservation->check_out)->format('M j, Y');
        }

        $roomtypes = Roomtype::where('user_id', auth()->user()->id)
        ->where('remove_status', false)
        ->get();

        $this->updatedByDate();

        $this->overallSales();

        // $this->refreshComponent();
        // $this->checkOut();


        return view('livewire.dashboard', compact('rooms','availableRooms','occupiedRooms','blockRooms','reservedRooms'));
    }

    public function updatedByDate(){

        $today = now()->toDateString();

        $query = BookingTransaction::where('user_id', auth()->user()->id);

        if ($this->by_date == 'today') {
            $query->whereDate('updated_at', now()->format('Y-m-d'));
        }
        elseif ($this->by_date == 'weekly') {
            $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);

        }elseif($this->by_date == 'monthly'){
            $query->whereMonth('updated_at', now()->month);

        }elseif($this->by_date == 'yearly'){
            $query->whereYear('updated_at', now()->year);

        }

        $this->bookingCount = $query->get();

        $this->arrival = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('check_in', $today)
        ->where('check_in_status', false)
        ->where('check_out_status', false)
        ->where('cancel_status', false)
        ->count();

        $this->departure = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('check_out', $today)
        ->where('check_in_status', true)
        ->where('check_out_status', false)
        ->where('cancel_status', false)
        ->count();

        $this->pendingCount = 0;
        $this->activeCount = 0;
        $this->inactiveCount = 0;
        $this->cancelledCount = 0;


        foreach ($this->bookingCount as $reservation) {
            $reservation->formatted_check_in = Carbon::parse($reservation->check_in)->format('M j, Y');
            $reservation->formatted_check_out = Carbon::parse($reservation->check_out)->format('M j, Y');

            $checkInStatus = $reservation->check_in_status;
            $checkOutStatus = $reservation->check_out_status;
            $cancelStatus = $reservation->cancel_status;

            if ($checkInStatus == 1 && $checkOutStatus == 0 && $cancelStatus == 0) {
                $this->activeCount++;
            } elseif ($checkInStatus == 0 && $checkOutStatus == 1 && $cancelStatus == 0) {
                $this->inactiveCount++;
            } elseif ($checkInStatus == 0 && $checkOutStatus == 0 && $cancelStatus == 1) {
                $this->cancelledCount++;
            } else {
                $this->pendingCount++;
            }
        }
    }

    public function redirectToRoom($status){
        
        return Redirect::to(route('rooms', ['status' => $status]));
    }

    public $by_date = 'monthly';
 
    public function redirectToBooking($status){
        
        return Redirect::to(route('transaction', ['status' => $status, 'date' => $this->by_date]));
    }

    public $sales_by_date = 'monthly';

    public function redirectToPos(){
        
        return Redirect::to(route('pos-reports', ['date' => $this->sales_by_date]));
    }

public function overallSales(){

    $this->reset('overallSales');
    
    $query = BookingTransaction::where('user_id', auth()->user()->id)
    ->where('cancel_status', false)
    ->where(function ($q){
        $q->where('check_in_status', true)
        ->orWhere('check_out_status', true);
    });

    if ($this->sales_by_date == 'today') {
        $query->whereDate('updated_at', now()->format('Y-m-d'));
    }
    elseif ($this->sales_by_date == 'weekly') {
        $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);

    }elseif($this->sales_by_date == 'monthly'){
        $query->whereMonth('updated_at', now()->month);

    }elseif($this->sales_by_date == 'yearly'){
        $query->whereYear('updated_at', now()->year);

    }

    $bookings = $query->get();

    $b_total = 0;
    $p_total = 0;

    foreach ($bookings as $book) {
        $b_total += $book->total_amount;

        $p_total += $book->order_cost;
    }

    $this->roomSales = $b_total;

    $this->productSales = $p_total;

    // $subquery = OrderTransaction::where('user_id', $this->user_id);

    // if ($this->sales_by_date == 'today') {
    //     $subquery->whereDate('created_at', now()->format('Y-m-d'));
    // }
    // elseif ($this->sales_by_date == 'weekly') {
    //     $subquery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);

    // }elseif($this->sales_by_date == 'monthly'){
    //     $subquery->whereMonth('created_at', now()->month);

    // }elseif($this->sales_by_date == 'yearly'){
    //     $subquery->whereYear('created_at', now()->year);

    // }

    // $orders = $subquery->get();
    // $totals = 0;

    // foreach ($orders as $order) {
    //     $totals += $order->total_amount;
    // }

    


    $this->overallSales = $this->roomSales + $this->productSales;
}

}
