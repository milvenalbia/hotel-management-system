@section('style')
 <style>
    html {
        scroll-behavior: smooth;
    }
 </style>
@endsection
<div class="card" style="background-color: #e6e9ed;" id="book-now">
    <div class="card-body">
    <div class="row">
        <div class="col-12 grid-margin stretch-card justify-content-between">
          <div class="card bg-dark">
            <div class="card-body py-0 px-0 px-sm-3">
              <div class="row align-items-center">
                <div class="col-lg-2 col-sm-2 col-xl-2">
                    <img src={{asset("admin/assets/images/dashboard/Group126@2x.png")}} class="img-fluid" alt="">
                </div>
                <div class="col-lg-4 col-sm-4 col-xl-4 pr-2">
                  <h3 class="mb-1 mb-sm-0">Room Reservation</h3>
                </div>
                <div class="col-lg-6 col-sm-6 col-xl-6 pl-0">
                    <input wire:model.live.debounce.200ms="search" class="form-control text-dark bg-white border-0 shadow rounded-pill" placeholder="Search Room Type">
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

    <div class="d-flex justify-content-end">
        <a href="#guest-list" class="btn btn-md font-weight-bold rounded btn-primary mb-3">Guest List<i class="ml-1 mdi mdi-arrow-down-bold-circle-outline"></i></a>
    </div>
    {{-- @php
        $colors = ['#6495ED', '#40E0D0', '#FFBF00', '#FF7F50', '#15FEF7', '#08FF13'];
        $colorIndex = 1;
        @endphp --}}
        {{-- style="border-left: 5px solid {{ $colors[$colorIndex % count($colors)] }};" --}}

        {{-- @php
        $colorIndex++; 
        @endphp --}}

    @if($roomtypes)
    <div class="row">
        
        @foreach($roomtypes as $roomtype)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card border-dark">
                    <h4 class="preview-subject text-center" id="overlay-text">{{$roomtype->roomtype}}</h4>
                <div class="card-body" style="padding: 18px">
                    <div>
                        <img src="{{ Storage::url($roomtype->image) }}" alt="Room Image" class="img-lg rounded">
                        <div id="overlay-img"></div>
                    </div>
                </div>
                <div class="card-footer text-dark">
                    @if($roomtype->available_rooms > 0)
                    <p class="text-small ">Available Rooms:&nbsp;{{ $roomtype->available_rooms }}</p>
                    @else
                    <p class="text-small">Available Rooms:&nbsp;No availabe room</p>
                    @endif
                    <p class="text-small">Room Capacity: &nbsp;Good for {{$roomtype->capacity}} person</p>
                    <p class="text-small">Room Price: &#8369;{{number_format($roomtype->price, 2, '.', ',')}}</p>
                    @if($roomtype->available_rooms < 1)
                    <button type="submit" style="font-size: 1rem; opacity: 20%; padding: 0.6rem;" class="btn font-weight-bold btn-md w-100 btn-primary text-center" wire:click="create({{ $roomtype->id }})" disabled>Book Now</button>
                    @else
                    <button type="submit" class="btn btn-md font-weight-bold btn-primary w-100 text-center" wire:click="openBookingModal({{ $roomtype->id }})" style="font-size: 1rem;padding: 0.6rem;">Book Now</button>
                    @endif
                </div>
            </div>
        </div>
        
        @endforeach
        </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $roomtypes->links() }}
            </div> 
    @endif

    <hr style="margin-top: 100px">

    <div class="card" id="guest-list">
        <div class="card-body">
    <div class="table-responsive">
        <div class="d-flex justify-content-between p-0 my-2">
            <h4 class="text-dark col-3">Reserved Guest List</h4>
    
            <a href="#book-now" class="btn btn-md rounded font-weight-bold btn-primary mb-3">Go Up<i class="ml-1 mdi mdi-arrow-up-bold-circle-outline"></i></a>
        </div>
        <div class="d-flex justify-content-between p-0 my-2">
            <div class="col-2 d-flex">
                <select class="form-control text-white w-50 bg-dark ml-1" wire:model.live.debounce.200ms="perPage">
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
                <input wire:model.live.debounce.200ms="guest_search" class="form-control bg-dark text-light w-50 mr-2" placeholder="Search Guest">
                <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="by_status" style="width: 20%;">
                    <option value="all">Filter by Status</option>
                    <option value="pending">Reserved</option>
                    <option value="arrival">Arrival Guest</option>
                </select>
            </div>
        </div>
            <table class="table table-hover text-dark">
              <thead>
                <tr>
                  <th> Guest Folio #</th>
                  <th> Room Type </th>
                  <th> Room </th>
                  <th> Name </th>
                  <th> Check In Date </th>
                  <th> Check Out Date </th>
                  <th> Status </th>
                  <th> Actions </th>
                </tr>
              </thead>
              <tbody>
                @if ($booking)
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
                            <td>{{ $formatted_check_in[$d] }}</td>
                            <td>{{ $formatted_check_out[$d] }}</td>
                            <td>
                                @if ($book->check_in_status == 0 && $book->check_out_status == 0 && $book->cancel == 0 && $book->check_in == $today)
                                    <div class="badge badge-primary">Arrival guest</div>
                                @else
                                    <div class="badge badge-warning">Reserved</div>
                                @endif
                            </td>
                            <td> 
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-md btn-info" wire:click="openConfirmationModal({{ $book->id }})">
                                            <i class="mdi mdi-account-key mr-1"></i>Register
                                        </a>
                                        <a href="#" class="btn btn-md btn-primary" wire:click="openModal({{ $book->id }})">
                                            <i class="mdi mdi-pencil-box-outline mr-1"></i>Edit
                                        </a>
                                    </div>
                            </td>
                        </tr>
                    @endforeach
            @endforeach
            
                    @else
                    <tr>
                        <td class="text-center" colspan="9">No booked guest available.</td>
                    </tr>
                @endif
              </tbody>
            </table>
            @if($guest_search && strlen($guest_search) > 2)
                {{-- Lieave it empty --}}
            @else
                <div class="d-flex justify-content-end mt-3">
                    {{ $booking->links() }}
                </div>
            @endif
          </div>
            </div>
        </div>
    
          {{-- Edit Modal --}}
          @livewire('edit-guest-record-modal')
    
          {{-- Check In Modal --}}
          @livewire('check-in-modal')
    
          <div wire:ignore.self class="modal fade" id="confirmModal" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to register '{{$firstname}} {{$lastname}}'?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">No</button>
                        <button class="btn btn-danger" wire:click="openCheckInModal({{$book_id}})">Yes</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- Booking Modal --}}
    @livewire('booking-modal')

        
</div>

</div>

@section('scripts')

@yield('booking-scripts')

<script>
    window.addEventListener('close-booking-modal', event =>{
        $('#createModal').modal('hide');
        $('#editGuestModal').modal('hide');
        $('#checkInModal').modal('hide');
        $('#confirmModal').modal('hide');
        $('#paypalPaymentModal').modal('hide');
        });
    
    window.addEventListener('show-paypal-payment', event =>{
        $('#paypalPaymentModal').modal('show');
    });
    
    window.addEventListener('show-booking-modal', event =>{
        $('#createModal').modal('show');
    });

    window.addEventListener('show-check-in-modal', event =>{
            $('#checkInModal').modal('show');
    });

    window.addEventListener('show-edit-booking-modal', event =>{
        $('#editGuestModal').modal('show');
    });

    window.addEventListener('show-confirm-modal', event =>{
        $('#confirmModal').modal('show');
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

<script>
    document.addEventListener('livewire:init', function () {
        const firstnameInput = document.getElementById('firstname');
        const lastnameInput = document.getElementById('lastname');

        firstnameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        lastnameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });
    });
</script>

@endsection
