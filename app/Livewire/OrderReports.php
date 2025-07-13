<?php

namespace App\Livewire;

use App\Models\OrderItem;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\Redirect;

class OrderReports extends Component
{
    use WithPagination;


    public $perPage = 10;

    public $byDate = '';
    public $startDate;
    public $endDate;
    public $reports;
    public $date_today;
    public $time;
    public $dateFormat;

    #[Url] 
    public $byCategory = '';

    #[Url] 
    public $search = '';

    public function render()
    {
        $today = Carbon::now();

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        if($this->perPage == 'all'){
            $this->perPage = 100000000;
        }

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

        if ($this->byCategory) {
            $query->whereHas('product', function ($subquery) {
                $subquery->where('category', $this->byCategory);
            });
        }

        if ($this->search && strlen($this->search) > 2) {
            $orderItems = $query->where(function ($query) {
                $query->whereHas('order.guest', function ($guestQuery) {
                    $guestQuery->where('firstname', 'like', '%' . $this->search . '%')
                        ->orWhere('lastname', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->with(['order', 'order.guest','product'])
            ->get();
        } else {
            $orderItems = $query->with(['order', 'order.guest','product'])
            ->paginate($this->perPage);
        }
        

        foreach($orderItems as $item){
            $this->dateFormat = Carbon::parse($item->created_at)->format('M d, Y h:i A');

            $createdAt = Carbon::parse($item->order->created_at);
            
            $hour = $createdAt->hour;

            if ($hour >= 5 && $hour < 12) {
                $this->time = 'Breakfast';
            } elseif ($hour >= 12 && $hour < 18) { // Adjusted condition for Lunch
                $this->time = 'Lunch';
            } else {
                $this->time = 'Dinner';
            }

        }
        
        
        return view('livewire.POS.order-reports', compact('orderItems'));
    }

    public function viewReports(){
        if(!empty($this->reports) && $this->reports == 'booking'){

            return Redirect::route('transaction');

        }elseif(!empty($this->reports) && $this->reports == 'pos'){

            return Redirect::route('pos-reports');
        }
    }
}
