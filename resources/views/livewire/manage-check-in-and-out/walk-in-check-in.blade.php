<div>
    <div class="d-flex justify-content-end">
        <ol class="breadcrumb text-large">
            <li class="breadcrumb-item"><i class="mdi mdi-subdirectory-arrow-left mr-1 text-primary"></i><a href="/checkInGuest">Go Back To New Guest</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Register Guest</li>
          </ol>
        
    </div>
<div class="card" style="background-color: #e6e9ed;">
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
                  <h3 class="mb-1 mb-sm-0">New Guest</h3>
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

    @if($roomtypes)
    <div class="row">
        {{-- @php
        $colors = ['#6495ED', '#40E0D0', '#FFBF00', '#FF7F50', '#15FEF7', '#08FF13'];
        $colorIndex = 1;
        @endphp --}}
        {{-- style="border-left: 5px solid {{ $colors[$colorIndex % count($colors)] }};" --}}
        @foreach($roomtypes as $roomtype)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card border-dark text-dark">
                    <h4 class="preview-subject text-center" id="overlay-text">{{$roomtype->roomtype}}</h4>
                <div class="card-body" style="padding: 18px">
                    <div>
                        <img src="{{ Storage::url($roomtype->image) }}" alt="Room Image" class="img-lg rounded pr-3">
                        <div id="overlay-img"></div>
                    </div>
                </div>
                <div class="card-footer">
                    @if($roomtype->available_rooms > 0)
                    <p class="text-small">Available Rooms:&nbsp;{{ $roomtype->available_rooms }}</p>
                    @else
                    <p class="text-small">Available Rooms:&nbsp;No availabe room</p>
                    @endif
                    <p class="text-small">Room Capacity: &nbsp;Good for {{$roomtype->capacity}} person</p>
                    <p class="text-small">Room Price: &#8369;{{number_format($roomtype->price, 2, '.', ',')}}</p>
                    @if($roomtype->available_rooms < 1)
                    <button type="submit" class="btn btn-md btn-primary w-100 text-center" style="font-size: 1rem; opacity: 20%; padding: 0.6rem;" wire:click="create({{ $roomtype->id }})" disabled>Register Guest</button>
                    @else
                    <button type="submit" class="btn btn-md btn-primary w-100 text-center" wire:click="openModal({{ $roomtype->id }})" style="font-size: 1rem;padding: 0.6rem;">Register Guest</button>
                    @endif
                </div>
            </div>
        </div>
        {{-- @php
        $colorIndex++; 
        @endphp --}}
        @endforeach
        </div>
        @if($search && strlen($search) > 2)
        {{-- leave it empty --}}
        @else
            <div class="d-flex justify-content-end">
                {{ $roomtypes->links() }}
            </div>
        @endif
        
    @endif

        {{-- Booking Modal --}}
        @livewire('walk-in-check-in-modal')
</div>

</div>
</div>

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var checkInInstance = flatpickr("#check_in", {
            dateFormat: "Y-m-d", // Set the date format to mm/dd/yyyy
            minDate: "today",   // Restrict to today and future dates
        });

        var checkOutInstance = flatpickr("#check_out", {
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
    window.addEventListener('close-booking-modal', event =>{
        $('#createModal').modal('hide');
        $('#paypalPaymentModal').modal('hide');
        });
    
    window.addEventListener('show-paypal-payment', event =>{
        $('#paypalPaymentModal').modal('show');
    });

    window.addEventListener('show-booking-modal', event =>{
        $('#createModal').modal('show');
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
