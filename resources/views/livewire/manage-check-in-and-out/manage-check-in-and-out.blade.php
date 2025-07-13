@section('css')
<link rel="stylesheet" href={{asset("print-css/print.css")}}>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
<div>
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
                  <h3 class="mb-1 mb-sm-0">New Guest List</h3>
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
      <div x-data="{show: true}" x-init="setTimeout(() => show = false, 7000)" x-show="show" class="alert-custom show showAlert">
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
        <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="filter" style="width: 20%;">
            <option value="">Filter by Status</option>
            <option value="pending">Reserved</option>
            <option value="checked in">In House</option>
            <option value="today">Arrival Guest</option>
            <option value="check out">Departing Guest</option>
        </select>
        <a href="/walk-in-check-in" class="btn btn-primary rounded ml-2 pt-2" style="height: 85%;"><i class="mdi mdi-plus-circle mr-1"></i> New Check In</a>
  </div>
  </div>

    <div class="row justify-content-center mt-3 tableToHideInPrint" >
          <div class="table-responsive">
            <table class="table table-hover text-dark">
              <thead>
                <tr>
                  <th> Guest Folio # </th>
                  <th> Room Type </th>
                  <th> Room </th>
                  <th> Name </th>
                  {{-- <th> Phone No. </th> --}}
                  <th> Arrival Date </th>
                  <th> Departure Date </th>
                  <th> Status</th>
                  <th> Actions </th>
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
                                    <img src="{{ Storage::url($firstRoomBooking->room->roomtypes->image) }}" alt="Room Image" class="img-fluid mr-0">
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
                            {{-- <td>{{ $book->guest->contact_no}}</td> --}}
                            <td>{{ $formatted_check_ins[$d] }}</td>
                            <td>
                                @if($book->extend_hours > 0 || $book->extend_days > 0)
                                    {{ $check_outs_with_times[$d] }}
                                @else
                                    {{ $formatted_check_outs[$d] }}
                                @endif
                            </td>
                            <td>
                                @if ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in != $today)
                                    <div class="badge badge-warning">Reserved</div>

                                @elseif ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->check_in == $today)
                                    <div class="badge badge-primary">Arrival Guest</div>

                                @elseif ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->check_out == $today)
                                    <div class="badge badge-danger">Departing Guest</div>

                                @elseif ($book->check_in_status == 0 && $book->check_out_status == 1)
                                    <div class="badge badge-dark text-small">Departed</div>
                                @else
                                    <div class="badge badge-success">In House</div>

                                @endif
                            </td>
                            <td> 
                                @if($book->check_in_status == 0 && $book->check_out_status == 0)
                                    <div class="btn-group">
                                    @if ($book->check_in_status == 0 && $book->check_out_status == 0)
                                        <a href="#" class="btn btn-md btn-info" wire:click.hover="openCheckInModal({{$book->id}})">
                                           <i class="mdi mdi-account-key mr-1"></i> Register
                                        </a>
                                    {{-- @elseif($book->check_in_status == 1 && $book->check_out_status == 0)
                                        @if(isset($disabledButtons[$book->id]) && $disabledButtons[$book->id])
                                            <button type="button" class="btn btn-md btn-success disabled">
                                                <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-md btn-success" wire:click="openCheckOutModal({{$book->id}})" wire:loading.attr="disabled">
                                                <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                            </button>
                                        @endif --}}
                                    @endif
                                        
                                        {{-- <a href="#" class="btn btn-md btn-primary  disabled" wire:click="openModal({{ $book->id }})" style="opacity: 20%;">
                                            <i class="mdi mdi-pencil-box-outline mr-1"></i>Edit
                                        </a>
                                        <a href="#" class="btn btn-md btn-sky  disabled" wire:click="openOrder({{ $book->id }})" style="opacity: 20%;">
                                            <i class="mdi mdi-food mr-1"></i>Order
                                        </a> --}}
                                    </div>
                                @else
                                    <div class="btn-group">

                                        @if ($book->check_in_status == 0 && $book->check_out_status == 0)
                                            <a href="#" class="btn btn-md btn-info " wire:click.hover="openCheckInModal({{$book->id}})">
                                                <i class="mdi mdi-account-key mr-1"></i>Register
                                            </a>
                                        {{-- @elseif($book->check_in_status == 1 && $book->check_out_status == 0)
                                            @if(isset($disabledButtons[$book->id]) && $disabledButtons[$book->id])
                                                <button type="button" class="btn btn-md btn-success mr-1 disabled">
                                                    <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-md btn-success" wire:click="openCheckOutModal({{$book->id}})" wire:loading.attr="disabled">
                                                    <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                                </button>
                                            @endif --}}
                                        @endif

                                        <a href="#" class="btn btn-md btn-primary " wire:click="openEditCheckInModal({{ $book->id }})">
                                            <i class="mdi mdi-pencil-box-outline mr-1"></i>Edit
                                        </a>
                                        {{-- <a href="{{ route('manage-order', ['bookId' => $book->id, '20?2310/0712?178dineInGuest' => '1']) }}" class="btn btn-md btn-sky ">
                                            <i class="mdi mdi-food mr-1"></i>Order
                                        </a> --}}
                                        {{-- <a href="/manage-order/{{$book->id}}" class="btn btn-sm btn-outline-success mr-2" >
                                            <i class="mdi mdi-food mr-1"></i>Dine In
                                        </a> --}}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
            @endforeach
            
                    @else
                    <tr>
                        <td class="text-center" colspan="9">No guest booking data is found.</td>
                    </tr>
                @endif
              </tbody>
            </table>
            @if($search && strlen($search) > 2)
                {{-- Leave it open --}}
            @else
                <div class="d-flex justify-content-end mt-2">
                    {{ $booking->links() }}
                </div>
            @endif  
          </div>
      </div>
    </div>
    <div class="card-footer tableToHideInPrint text-light">
        <div class="d-flex justify-content-between mt-2">
            <p class="text-small w-100 bg-success p-2">Guest has already checked in.</p>
            <p class="text-small w-100 bg-warning p-2">Guest has not checked in yet.</p>
            <p class="text-small w-100 bg-primary p-2">Guest is going to check in today.</p>
            <p class="text-small w-100 bg-danger p-2">Guest is going to check out today.</p>
        </div>
    </div>

      {{-- Edit Check In Modal --}}
      @livewire('edit-checked-in-guest')

      {{-- Check In Modal --}}
      @livewire('check-in-modal')

      {{-- Check Out Modal --}}
      {{-- @livewire('check-out-modal') --}}

</div>

</div>

@section('scripts')
    <script>
         document.addEventListener("DOMContentLoaded", function() {
        var checkInInstance = flatpickr(".check_in", {
            dateFormat: "Y-m-d", // Set the date format to mm/dd/yyyy
            minDate: "today",   // Restrict to today and future dates
        });

        var checkOutInstance = flatpickr(".check_out", {
            dateFormat: "Y-m-d", // Set the date format to mm/dd/yyyy
            minDate: "today", // Restrict past dates
        });

        checkInInstance.config.onChange.push(function(selectedDates, dateStr, instance) {
            // Update the minimum allowed date for check-out based on the selected check-in date
            checkOutInstance.set("minDate", dateStr);
        });
    });
    </script>

    <script>
        window.addEventListener('close-guest-booking-modal', event =>{
            $('#deleteModal').modal('hide');
            $('#editCheckInModal').modal('hide');
            $('#checkInModal').modal('hide');
            $('#checkOutModal').modal('hide');
            $('#autoDelete').modal('hide');
            $('#paypalPaymentModal').modal('hide');
        });
    
        window.addEventListener('show-paypal-payment', event =>{
            $('#paypalPaymentModal').modal('show');
        });

        window.addEventListener('show-delete-booking-modal', event =>{
            $('#deleteModal').modal('show');
        });
    
        window.addEventListener('show-check-in-modal', event =>{
            $('#checkInModal').modal('show');
        });

        window.addEventListener('show-edit-check-in-modal', event =>{
            $('#editCheckInModal').modal('show');
        });

        window.addEventListener('show-conflict-modal', event =>{
            $('#conflictModal').modal('show');
        });

        window.addEventListener('close-conflict-modal', event =>{
            $('#conflictModal').modal('hide');
        });

        window.addEventListener('show-info-modal', event =>{
            $('#infoModal').modal('show');
        });

        window.addEventListener('close-info-modal', event =>{
            $('#infoModal').modal('hide');
        });

        window.addEventListener('show-roomtype-modal', event =>{
            $('#roomtypeModal').modal('show');
        });

        window.addEventListener('close-roomtype-modal', event =>{
            $('#roomtypeModal').modal('hide');
        });

        // window.addEventListener('show-check-out-modal', event =>{
        //     $('#checkOutModal').modal('show');
        // });

        // window.addEventListener('show-auto-delete-modal', event =>{
        //     $('#autoDelete').modal('show');
        // });

        window.addEventListener('print-check-out', event =>{
            window.print();
        });

        window.addEventListener('close-payment-method-modal', event =>{
            $('#cardModal').modal('hide');
            $('#paypalModal').modal('hide');
        });
    
        window.addEventListener('show-paypal-modal', event =>{
            $('#paypalModal').modal('show');
        });
    
        window.addEventListener('show-card-modal', event =>{
            $('#cardModal').modal('show');
        });
    
    </script>

    @yield('check-in-scripts')

    {{-- @yield('check-out-scripts') --}}
    @endsection
