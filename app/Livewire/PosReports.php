<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\OrderItem;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\OrderTransaction;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Redirect;

class PosReports extends Component
{

    use WithPagination;

    public $reports;

    public $startDate;
    public $endDate;
    public $today;
    public $date_today;
    public $overall_total;
    public $perPage = 10;
    public $formatted_check_in;
    public $formatted_check_out;

    public $byDate = '';

    #[Url]
    public $search = '';

    public function mount($date = null){
        $this->byDate = $date;
    }
    
    public function render()
    {

        $today = Carbon::now();

        $this->today = today()->format('Y-m-d');

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        $query = BookingTransaction::query()
        ->where('user_id', auth()->user()->id)
        ->where(function ($q){
            $q->where('check_in_status', true)
            ->orWhere('check_out_status', true)
            ->orWhere('cancel_status', true);
        });

        if ($this->byDate == 'today') {
            $query->whereDate('updated_at', now()->format('Y-m-d'));
        }
        elseif ($this->byDate == 'weekly') {
            $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);

        }elseif($this->byDate == 'monthly'){
            $query->whereMonth('updated_at', now()->month);

        }elseif($this->byDate == 'yearly'){
            $query->whereYear('updated_at', now()->year);

        }elseif($this->byDate == 'date-range'){
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('updated_at', [$this->startDate, $this->endDate]);
            }
        }

        if($this->search && strlen($this->search) > 2){
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

        $total = 0 ;
        foreach ($booking as $reservation) {
            $this->formatted_check_in = Carbon::parse($reservation->check_in)->format('M j, Y');
            $this->formatted_check_out = Carbon::parse($reservation->check_out)->format('M j, Y');
            if($reservation->cancel_status == 0){
                $total += $reservation->total_amount + $reservation->order_cost;
            }
            
        }

        $this->overall_total = $total;

        $orders = OrderTransaction::where('user_id', auth()->user()->id)->get();

        $orderIds = $orders->pluck('id');

        $query = OrderItem::query()
        ->whereIn('order_transaction_id', $orderIds);

        if ($this->byDate == 'today') {
            $query->whereDate('updated_at', now()->format('Y-m-d'));
        } elseif ($this->byDate == 'weekly') {
            $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->byDate == 'monthly') {
            $query->whereMonth('updated_at', now()->month);
        } elseif ($this->byDate == 'yearly') {
            $query->whereYear('updated_at', now()->year);
        } elseif ($this->byDate == 'date-range' && $this->startDate && $this->endDate) {
            $query->whereBetween('updated_at', [$this->startDate, $this->endDate]);
        }

        $orderItems = $query->where(function ($query) {
                $query->whereHas('order.guest', function ($guestQuery) {
                    $guestQuery->where('firstname', 'like', '%' . $this->search . '%')
                        ->orWhere('lastname', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
        })
            ->with(['order', 'order.guest','product'])
            ->paginate($this->perPage);

        return view('livewire.POS.pos-reports', compact('booking','orderItems'));
    }

    public function viewReports(){
        if(!empty($this->reports) && $this->reports == 'booking'){

            return Redirect::route('transaction');

        }elseif(!empty($this->reports) && $this->reports == 'order'){

            return Redirect::route('order-reports');
        }
    }
}
