@section('css')
    <link rel="stylesheet" href={{asset("print-css/print.css")}}>
@endsection
<div>
    <div class="d-flex justify-content-between">
        <div class="container">
            <h3 class="mb-0 text-dark">Reservation Reports</h3>
            <ol class="breadcrumb text-large">
                <li class="breadcrumb-item"><i class="mdi mdi-subdirectory-arrow-left mr-1 text-primary"></i><a href="/dashboard">Go To Dashboard</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Reservation Reports</li>
            </ol>
        </div>
        <div class="col-8 d-flex justify-content-end mt-2">
            <select class="form-control text-white bg-dark" wire:model.live="reports" style="width: 35%; height: 45px;">
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
                      <h3 class="mb-1 mb-sm-0">Guest List</h3>
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
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option> 
                <option value="50">50</option>  
                <option value="100">100</option>  
            </select>
            <p class="text-dark text-small ml-2 mt-2">Per Page</p>
        </div>
        <div class="col-10 d-flex justify-content-end">
            @if($byDate == 'date-range')
            <input wire:model.live="startDate" type="date" class="form-control bg-white text-dark mr-1" placeholder="date start" style="width: 20%;">
            <p class="text-small text-dark mr-1 mt-2">To</p>
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
            <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="byStatus" style="width: 20%;">
                <option value="All">Filter by Status</option>
                <option value="Reserved">Reserved</option>
                <option value="In House">In House</option> 
                <option value="Departed Guest">Departed Guest</option>
                <option value="Arrival Guest">Arrival Guest</option>
                <option value="Departing Guest">Departing Guest</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            <button onclick="printTable()" class="btn btn-primary rounded ml-2" style="height: 84%;"><i class="mdi mdi-printer mr-1"></i>Generate Reports</button>
        </div>
    </div>

        <div class="row justify-content-center mt-3 tableToHideInPrint" >
              <div class="table-responsive">
                <table class="table text-dark">
                  <thead>
                    <tr>
                      <th> # </th>
                      <th> Room Type </th>
                      <th> Room </th>
                      <th> Name </th>
                      <th> Phone No. </th>
                      <th> Arrival Date </th>
                      <th> Departure Date </th>
                      <th> Status </th>
                    </tr>
                  </thead>
                  <tbody>
                    @if ($booking->count() > 0)
                    @foreach ($booking as $d => $book)
                        @foreach ($book->roomBooking->groupBy('booking_transaction_id') as $bookingId => $roomBookings)
                            @php
                                $firstRoomBooking = $roomBookings->first();
                            @endphp
                            <tr>
                                <td>{{ $book->folio_no }}</td>
                                <td>
                                    @if ($firstRoomBooking->room->roomtypes)
                                        <img src="{{ Storage::url($firstRoomBooking->room->roomtypes->image) }}" alt="Room Image" class="img-fluid mr-1">
                                        <span class="text-small">{{ $firstRoomBooking->room->roomtypes->roomtype }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->room > 1)
                                        {{$book->room}} Rooms
                                    @else
                                        @foreach($roomBookings as $room)
                                            {{$room->room->room_no}}
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $book->guest->firstname }}&nbsp;{{ $book->guest->lastname }}</td>
                                <td>{{ $book->guest->contact_no}}</td>
                                <td>{{ $formatted_check_in[$d] }}</td>
                                <td>{{ $formatted_check_out[$d] }}</td>
                                <td>
                                    @if ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in != $today && $book->cancel_status == 0)

                                        <div class="badge badge-outline-warning">Reserved Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in == $today && $book->cancel_status == 0)
                                        
                                        <div class="badge badge-outline-primary">Arrival Guest</div>

                                    @elseif ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel_status == 0 && $book->check_out == $today)

                                    <div class="text-small" style="display: inline-block;
                                    padding: 0.25em 0.7em;
                                    font-size: .75rem;
                                    font-weight: 700;
                                    line-height: 2;
                                    text-align: center;
                                    white-space: nowrap;
                                    vertical-align: baseline;
                                    border-radius: 0.25rem;
                                    border:1px solid #861e1e;
                                     color: #7a1f1f;">Departing Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 1 && $book->cancel_status == 0)

                                        <div class="badge badge-outline-danger">Departed Guest</div>

                                    @elseif ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel_status == 0)

                                        <div class="badge badge-outline-success">In House Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->cancel_status == 1)

                                        <div class="badge badge-outline-dark">Cancelled</div>

                                    @endif
                            
                                </td>
                            </tr>
                        @endforeach
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
                        {{ $booking->links() }}
                    </div>
                @endif
                
              </div>
          </div>
          <div class="card-footer tableToHideInPrint text-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-small rounded bg-success p-2">Guest has already checked in.</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-small rounded bg-warning p-2">Guest has not checked in yet.</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-small rounded bg-danger p-2">Guest has already checked out.</p>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-small rounded bg-primary p-2">Guest is going to check in today.</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-small rounded p-2" style="background: #471818;">Guest is going to check out today.</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-small rounded bg-dark p-2" style="background: #566573;">Cancelled Reservation.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table for Print --}}
        <div class="container" id="tableToPrint">
            <center>
                <h1 class="mb-5 text-dark">
                    @if($byStatus != null)
                        "{{$byStatus}} Reports"
                    @else
                        "All Reports"
                    @endif
                    
                </h1>
            </center>

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
                      <th> Guest Folio #</th>
                      <th> Room Type </th>
                      <th> Room </th>
                      <th> Name </th>
                      <th> Phone No. </th>
                      <th> Arrival Date </th>
                      <th> Departure Date </th>
                    </tr>
                  </thead>
                  <tbody>
                    @if ($booking->count() > 0)
                    @foreach ($booking as $d => $book)
                        @foreach ($book->roomBooking->groupBy('booking_transaction_id') as $bookingId => $roomBookings)
                            @php
                                $firstRoomBooking = $roomBookings->first();
                            @endphp
                            <tr>
                                <td>{{ $book->folio_no }}</td>
                                <td>
                                    @if ($firstRoomBooking->room->roomtypes)
                                        @if($firstRoomBooking->room->roomtypes->roomtype == 'Presidential Suite')
                                        Pres. Suite
                                        @elseif($firstRoomBooking->room->roomtypes->roomtype == 'Junior Suite')
                                        Jr. Suite
                                        @elseif($firstRoomBooking->room->roomtypes->roomtype == 'Standard Single')
                                        Std. Single
                                        @elseif($firstRoomBooking->room->roomtypes->roomtype == 'Standard Double')
                                        Std. Double
                                        @elseif($firstRoomBooking->room->roomtypes->roomtype == 'Standard Triple')
                                        Std. Triple
                                        @elseif($firstRoomBooking->room->roomtypes->roomtype == 'Standard Quad')
                                        Std. Quad
                                        @else
                                        {{ $firstRoomBooking->room->roomtypes->roomtype }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($book->room > 1)
                                        {{$book->room}} Rooms
                                    @else
                                        @foreach($roomBookings as $room)
                                            {{$room->room->room_no}}
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $book->guest->firstname }}&nbsp;{{ $book->guest->lastname }}</td>
                                <td>{{ $book->guest->contact_no}}</td>
                                <td>{{ $formatted_check_in[$d] }}</td>
                                <td>{{ $formatted_check_out[$d] }}</td>
                                {{-- <td>
                                    @if ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in != $today && $book->cancel_status == 0)

                                        <div class="badge badge-outline-warning">Reserved Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in == $today && $book->cancel_status == 0)
                                        
                                        <div class="badge badge-outline-primary">Arrival Guest</div>

                                    @elseif ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel_status == 0 && $book->check_out == $today)

                                        <div class="badge badge-outline-dark text-small">Departing Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 1 && $book->cancel_status == 0)

                                        <div class="badge badge-outline-danger">Departed Guest</div>

                                    @elseif ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel_status == 0)

                                        <div class="badge badge-outline-success">In House Guest</div>

                                    @elseif ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->cancel_status == 1)

                                        <div class="badge badge-outline-dark">Cancelled</div>

                                    @endif
                            
                                </td> --}}
                            </tr>
                        @endforeach
                @endforeach
                
                        @else
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>No booked guest record</td>
                            <td></td>
                            <td></td>
                            <td></td>
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
