<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Redirect;

class Transactions extends Component
{

    use WithPagination;


    public $perPage = 10;

    public $byDate = '';
    public $startDate;
    public $endDate;
    public $reports;
    public $date_today;
    public $today;

    public function mount($status = null, $date = null){
        $this->byStatus = $status;
        $this->byDate = $date;
    }

    #[Url] 
    public $byStatus = 'All';

    #[Url] 
    public $search = '';

    public $formatted_check_in;
    public $formatted_check_out;

    public function render()
    {

        $today = Carbon::now();

        $this->today = today()->format('Y-m-d');

        $this->date_today = Carbon::parse($today)->format('F j, Y h:i A');

        $query = BookingTransaction::query()
        ->where('user_id', auth()->user()->id);

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

        if ($this->byStatus == 'Reserved') {
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->where('check_in', '!=' , $this->today);
        }
        elseif ($this->byStatus == 'In House') {
            $query->where('check_in_status', true)
            ->where('check_out_status', false)
            ->where('cancel_status', false);
        }elseif($this->byStatus == 'Departed Guest'){
            $query->where('check_in_status', false)
            ->where('check_out_status', true)
            ->where('cancel_status', false);
        }elseif($this->byStatus == 'Cancelled'){
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('cancel_status', true);
        }elseif($this->byStatus == 'Arrival Guest'){
            $query->where('check_in_status', false)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->where('check_in', $this->today);
        }elseif($this->byStatus == 'Departing Guest'){
            $query->where('check_in_status', true)
            ->where('check_out_status', false)
            ->where('cancel_status', false)
            ->where('check_out', $this->today);
        }

        if ($this->search && strlen($this->search) > 2) {
            $booking = $query->where(function($query) {
                $query->whereHas('guest', function ($typeQuery) {
                    $typeQuery->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->with('guest')
            ->get();
        } else {
            $booking = $query->with('guest')
            ->paginate($this->perPage);
        }
        
        
        $this->formatted_check_in = [];
        $this->formatted_check_out = [];
        foreach ($booking as $reservation) {
            $this->formatted_check_in[] = Carbon::parse($reservation->check_in)->format('M j, Y');
            $this->formatted_check_out[] = Carbon::parse($reservation->check_out)->format('M j, Y');
        }

        // $this->autoCancelShowModal();
       
        return view('livewire.POS.transactions',compact('booking'),);
    }

    public function viewReports(){
        if(!empty($this->reports) && $this->reports == 'pos'){

            return Redirect::route('pos-reports');

        }elseif(!empty($this->reports) && $this->reports == 'order'){

            return Redirect::route('order-reports');
        }
    }
}
