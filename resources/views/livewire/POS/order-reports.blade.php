@section('css')
    <link rel="stylesheet" href={{asset("print-css/print.css")}}>
@endsection
<div>
    <div class="d-flex justify-content-between">
        <div class="container">
            <h3 class="mb-0 text-dark">Order Transaction Reports</h3>
            <ol class="breadcrumb text-large">
                <li class="breadcrumb-item"><i class="mdi mdi-subdirectory-arrow-left mr-1 text-primary"></i><a href="/dashboard">Go To Dashboard</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Order Transaction Reports</li>
            </ol>
        </div>
        <div class="col-7 d-flex justify-content-end mt-2">
            <select class="form-control text-white bg-dark" wire:model.live="reports" style="width: 40%; height: 45px;">
                <option value="">Select to Display Reports</option>
                <option value="booking">Reservation Reports</option>
                <option value="order">Order Reports</option>
                <option value="pos">Point of Sale</option> 
            </select>
            <button wire:click="viewReports" type="button" class="btn btn-primary" style="width: auto; height: 45px;">
                <i class="mdi mdi-subdirectory-arrow-right mr-1" style="font-size: 20px"></i><span style="font-size: 18px">Go</span>
            </button>
        </div>
    </div>

<div class="card px-0" style="background-color: #e6e9ed;">
    <div class="card-body">
    <div class="row">
        <div class="col-12 grid-margin stretch-card justify-content-between">
          <div class="card bg-dark">
            <div class="card-body py-0 px-0 px-sm-3">
              <div class="row align-items-center">
                <div class="col-lg-2 col-sm-2 col-xl-2">
                  <img src={{ asset("admin/assets/images/dashboard/Group126@2x.png")}} class="img-fluid" alt="">
                </div>
                <div class="col-lg-4 col-sm-4 col-xl-4 pr-2">
                  <h3 class="mb-1 mb-sm-0">Order Transaction List</h3>
                </div>
                <div class="col-lg-6 col-sm-6 col-xl-6 pl-0">
                    <input wire:model.live.debounce.200ms="search" class="form-control text-dark bg-white border-0 shadow rounded-pill" placeholder="Search guest name...">
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

    @if(Session::has('success'))
      <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom show showAlert">
          <span class="fas fa-check-circle ml-2"></span>
          <span class="text-white text-sm ml-5">{{session('success')}}</span>
      </div>
  @endif

  @if(Session::has('message'))
      <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom-message show showAlert">
          <span class="fas fa-info-circle ml-2"></span>
          <span class="text-white text-sm ml-5">{{session('message')}}</span>
      </div>
  @endif

  <div class="d-flex justify-content-between p-0 tableToHideInPrint">
    <div class="col-2 d-flex">
        <select class="form-control text-white w-50 bg-dark" wire:model.live.debounce.200ms="perPage" >
            <option value="all">All</option>
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option> 
            <option value="50">50</option>  
            <option value="100">100</option>  
        </select>
        <p class="text-dark text-small ml-2 mt-2 ">Per Page</p>
    </div>
    <div class="col-10 d-flex justify-content-end">
        @if($byDate == 'date-range')
        <input wire:model.live="startDate" type="date" class="form-control bg-white text-dark mr-1" placeholder="date start" style="width: 20%;">
        <p class="text-small mr-1 mt-2 text-dark">To</p>
        <input wire:model.live="endDate" type="date" class="form-control bg-white text-dark mr-2" style="width: 20%;">
        @endif
        <select class="form-control text-white mr-2 bg-dark" wire:model.live.debounce.200ms="byDate" style="width: 20%;">
            <option value="all">Filter by Date</option>
            <option value="today">Today</option>
            <option value="weekly">This Week</option> 
            <option value="monthly">This Month</option>
            <option value="yearly">This Year</option>
            <option value="date-range">Date Range</option>
        </select>
        <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="byCategory" style="width: 20%;">
            <option value="">Filter by Category</option>
            <option value="Appetizer">Appetizer</option>
            <option value="Salad">Salad</option>
            <option value="Main Course">Main Course</option>
            <option value="Dessert">Dessert</option>
            <option value="Beverage">Beverage</option> 
        </select>
        <button onclick="printTable()" class="btn btn-primary rounded ml-2" style="height: 84%;"><i class="mdi mdi-printer mr-1"></i>Generate Reports</button>
    </div>
</div>

    <div class="row justify-content-center mt-3 tableToHideInPrint" >
          <div class="table-responsive">
            <table class="table table-bordered text-dark">
              <thead>
                <tr class="table-bordered" style="border: 1px solid #525b72">
                  <th> # </th>
                  <th> Guest Name</th>
                  <th> Phone No.</th>
                  <th> Products </th>
                  <th> Quantity </th>
                  <th> Price </th>
                  <th> Total Price </th>
                  <th> Date Pruchase </th>
                </tr>
              </thead>
              <tbody>
                @if ($orderItems->count() > 0)
                @foreach ($orderItems->groupBy('order_transaction_id') as $orderTransactionItems)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $orderTransactionItems->first()->order->guest->firstname }} {{ $orderTransactionItems->first()->order->guest->lastname }}
                        </td>
                        <td>
                            {{ $orderTransactionItems->first()->order->guest->contact_no }}
                        </td>
                        <td colspan="5">
                            @if($orderTransactionItems->first()->order->created_at->hour >= 5 && $orderTransactionItems->first()->order->created_at->hour < 12)
                                <h4>Breakfast</h4>
                            @elseif ($orderTransactionItems->first()->order->created_at->hour >= 12 && $orderTransactionItems->first()->order->created_at->hour < 18)
                                <h4>Lunch</h4>
                            @else
                                <h4>Dinner</h4>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small"><span class="text-small">{{ $loop->iteration }}.</span> {{ $item->product->product_name }}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">{{ $item->quantity }}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">&#8369;{{number_format($item->product->product_price, 2,'.',',')}}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">&#8369;{{number_format($item->total_price, 2,'.',',')}}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">{{$item->created_at->format('M j, Y h:i A')}}</p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                    @else
                    <tr>
                        <td class="text-center" class="text-center" colspan="8">No record is found.</td>
                    </tr>
                @endif
              </tbody>
            </table>
            @if ($search && strlen($search) > 2)
                {{-- leave it empty --}}
            @else
                <div class="d-flex justify-content-end mt-3">
                    {{ $orderItems->appends(request()->query())->links() }}
                </div>
            @endif
          </div>
      </div>

    {{-- Table for Print --}}
    <div class="container" id="tableToPrint">
        <center><h1 class="mb-5 text-dark">"Reports"</h1></center>

        <div class="d-flex container">
            <div class="container mt-2 col-8 ml-0 text-dark">
                <h2 class="text-uppercase">{{auth()->user()->hotel_name}}</h2>
            </div>
            <div class="container col-4 mr-0 mb-4 text-dark" style="margin-left: -280px;">
                <p class="text-small mb-1" >Date: {{$date_today}}</p>
                <p class="text-small mb-1" style="margin-left: 98px;">Admin: {{auth()->user()->name}}</p>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
              <tr>
                <th> # </th>
                <th> Guest Name</th>
                <th> Phone No.</th>
                <th> Products </th>
                <th> Quantity </th>
                <th> Price </th>
                <th> Total Price </th>
                <th> Date Pruchase </th>
              </tr>
            </thead>
            <tbody>
                @if ($orderItems->count() > 0)
                @foreach ($orderItems->groupBy('order_transaction_id') as $orderTransactionItems)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $orderTransactionItems->first()->order->guest->firstname }} {{ $orderTransactionItems->first()->order->guest->lastname }}
                        </td>
                        <td>
                            {{ $orderTransactionItems->first()->order->guest->contact_no }}
                        </td>
                        <td colspan="5">
                            @if($orderTransactionItems->first()->order->created_at->hour >= 5 && $orderTransactionItems->first()->order->created_at->hour < 12)
                                <h4>Breakfast</h4>
                            @elseif ($orderTransactionItems->first()->order->created_at->hour >= 12 && $orderTransactionItems->first()->order->created_at->hour < 18)
                                <h4>Lunch</h4>
                            @else
                                <h4>Dinner</h4>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small"><span class="text-small">{{ $loop->iteration }}.</span> {{ $item->product->product_name }}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">{{ $item->quantity }}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">&#8369;{{number_format($item->product->product_price, 2,'.',',')}}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">&#8369;{{number_format($item->total_price, 2,'.',',')}}</p>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($orderTransactionItems as $item)
                                <p class="text-small">{{$item->created_at->format('M j, Y h:i A')}}</p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                    @else
                    <tr>
                        <td class="text-center" class="text-center" colspan="8">No record is found.</td>
                    </tr>
                @endif
              </tbody>
            </table>


                <h4 class="text-dark mt-5 mb-4">CONFORME:</h4>
                <div style="border-top: 1px solid #000000; width: 140px;"></div>
                <p class="text-small text-dark">Name and Signature</p>
          </div>
          

</div>

</div>

</div>

@section('scripts')
<script>
    function printTable() {
        window.print();
    }
</script>
@endsection
