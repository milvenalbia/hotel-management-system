@section('css')
    <link rel="stylesheet" href={{asset("print-css/print.css")}}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
<div class="card px-0" style="background-color: #e6e9ed;">
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
                      <h3 class="mb-1 mb-sm-0">Guest Booking List</h3>
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

      <div class="d-flex justify-content-between p-0">
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
            <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="byStatus" style="width: 20%;">
                <option value="all">Filter by Status</option>
                <option value="pending">Reserved</option>
                <option value="active">In House</option>
                <option value="check out">Departing Guest</option>  
            </select>
        </div>
    </div>

        <div class="row justify-content-center mt-3">
              <div class="table-responsive">
                <table class="table table-hover text-dark">
                  <thead>
                    <tr>
                      <th> Guest Folio #</th>
                      <th> Room Type </th>
                      <th> Room </th>
                      <th> Name </th>
                      <th> Phone No. </th>
                      <th> Check In Date </th>
                      <th> Check Out Date </th>
                      <th> Status </th>
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
                                <td>
                                    {{ $book->folio_no }}
                                </td>
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
                                <td>{{ $book->guest->contact_no}}</td>
                                <td>{{ $formatted_check_ins[$d] }}</td>
                                <td>
                                    {{ $formatted_check_outs[$d] }}
                                </td>
                                <td>
                                    @if ($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel == 0 && $book->check_out != $today)
                                        <div class="badge badge-success">In House</div>
                                    @elseif($book->check_in_status == 1 && $book->check_out_status == 0 && $book->cancel == 0 && $book->check_out == $today)
                                    <div class="badge badge-danger">Departing Guest</div>
                                    @else
                                        <div class="badge badge-warning">Reserved</div>
                                    @endif
                                </td>
                                <td> 
                                    @if($book->check_in_status == 0 && $book->check_out_status == 0)
                                        <div class="btn-group">
                                            @if(isset($disabled[$book->id]) && $disabled[$book->id])
                                                <button type="button" class="btn btn-md btn-info disabled">
                                                    <i class="mdi mdi-eye mr-1"></i>View
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-md btn-info" wire:click="viewModal({{ $book->id }})" wire:loading.attr="disabled">
                                                    <i class="mdi mdi-eye mr-1"></i>View
                                                </button>
                                            @endif
                                            <a href="#" class="btn btn-md btn-primary" wire:click="openModal({{ $book->id }})">
                                                <i class="mdi mdi-pencil-box-outline mr-1"></i>Edit
                                            </a>
                                            <a href="#" class="btn btn-md btn-danger" wire:click="deleteModal({{ $book->id }})">
                                                <i class="mdi mdi-delete mr-1"></i>Cancel
                                            </a>
                                        </div>
                                    @else
                                        <div class="btn-group">
                                            @if(isset($disabled[$book->id]) && $disabled[$book->id])
                                                <button type="button" class="btn btn-md btn-info disabled">
                                                    <i class="mdi mdi-eye mr-1"></i>View
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-md btn-info" wire:click="viewModal({{ $book->id }})" wire:loading.attr="disabled">
                                                    <i class="mdi mdi-eye mr-1"></i>View
                                                </button>
                                            @endif
                                            @if(isset($disabledButtons[$book->id]) && $disabledButtons[$book->id])
                                                <button type="button" class="btn btn-md btn-success disabled">
                                                    <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-md btn-success" wire:click="openCheckOutModal({{$book->id}})" wire:loading.attr="disabled">
                                                    <i class="fa-solid fa-person-walking-arrow-right mr-1"></i>Check-Out
                                                </button>
                                            @endif
                                            <a href="#" class="btn btn-md btn-sky" wire:click="confirmOrderModal({{$book->id}})">
                                                <i class="mdi mdi-food mr-1"></i>Order
                                            </a>
                                        </div>
                                    @endif
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
                @if($search && strlen($search) > 2)
                    {{-- leave it empty --}}
                @else
                    <div class="d-flex justify-content-end mt-3">
                        {{ $booking->links() }}
                    </div>
                @endif
                
              </div>
          </div>
          <div class="card-footer tableToHideInPrint text-light">
            <div class="d-flex justify-content-between mt-2">
                <p class="text-small w-100 bg-success p-2">Guest has already checked in.</p>
                <p class="text-small w-100 bg-warning p-2">Guest has not checked in yet.</p>
                <p class="text-small w-100 bg-danger p-2">Guest is going to check out today.</p>
            </div>
        </div>

        {{-- Order Confirmation modal --}}
            <div wire:ignore.self class="modal fade" id="confirmOrderModal" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog border-light" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmOrderModalLabel">Confirmation</h5>
                            <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            Are you sure '{{$name}}' is going to order?
                        </div>
                        @if($guest_id)
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="cancelOrder()" data-dismiss="modal">Back</button>
                                <button type="button" class="btn btn-primary" wire:click="goToOrder()">Yes</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        


            
        {{-- Auto Cancel --}}
        <div wire:ignore.self class="modal fade" id="autoDelete" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #191c24">
                        <h5 class="modal-title" id="deleteModalLabel">Notification</h5>
                    </div>
                    <div class="modal-body" style="background-color: #191c24">
                        <h3 class="text-warning"><center>Important Notice</center></h3>
                        <p class="text-white" style="margin-top: -20px;">
                            <center>
                            <br>Guest did'nt check in within 24 hours.
                            <br>Booking will be cancel!
                            </center>
                        </p>
                            <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-primary" wire:click="cancelReservation()">Ok</button>
                            </div>
                    </div>
                </div>
            </div>
        </div>

          {{-- Edit Modal --}}
          @livewire('edit-guest-record-modal')

          {{-- View Modal --}}
          @livewire('view-guest-modal')

          {{-- Delete Modal --}}
          @livewire('delete-booking-modal')

          {{-- Check Out Modal --}}
        @livewire('check-out-modal')
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
            $('#editGuestModal').modal('hide');
            $('#viewGuestModal').modal('hide');
            $('#autoDelete').modal('hide');
            $('#checkOutModal').modal('hide');
            $('#confirmOrderModal').modal('hide');
            $('#paypalPaymentModal').modal('hide');
        });

        window.addEventListener('show-order-confirmation-modal', event =>{
            $('#confirmOrderModal').modal('show');
        });
    
        window.addEventListener('show-paypal-payment', event =>{
            $('#paypalPaymentModal').modal('show');
        });

        window.addEventListener('show-delete-booking-modal', event =>{
            $('#deleteModal').modal('show');
        });

        window.addEventListener('print-check-out', event =>{
            window.print();
        });
    
        window.addEventListener('show-edit-booking-modal', event =>{
            $('#editGuestModal').modal('show');
        });

        window.addEventListener('view-guest-modal', event =>{
            $('#viewGuestModal').modal('show');
        });

        window.addEventListener('show-auto-delete-modal', event =>{
            $('#autoDelete').modal('show');
        });

        window.addEventListener('show-check-out-modal', event =>{
            $('#checkOutModal').modal('show');
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

        window.addEventListener('show-info-modal', event =>{
            $('#infoModal').modal('show');
        });

        window.addEventListener('close-info-modal', event =>{
            $('#infoModal').modal('hide');
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


</div>

