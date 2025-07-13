<div>

    @if(Session::has('success'))
        <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom show showAlert">
            <span class="fas fa-check-circle ml-2"></span>
            <span class="text-white text-md ms-5">{{session('success')}}</span>
        </div>
    @endif

    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Book Now</h2>
                <h3 class="section-subheading text-dark">Have a refreshing stay to {{auth()->user()->hotel_name}}</h3>
            </div>
            <div class="row">
                @if($roomtypes)
                    @foreach($roomtypes as $roomtype)
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <!-- Portfolio item 1-->
                            <div class="portfolio-item">
                                @if($roomtype->available_rooms > 0)
                                    <a class="portfolio-link " wire:click.hover="openModal({{$roomtype->id}})">
                                        <div class="portfolio-hover">
                                            <div class="portfolio-hover-content">BOOK NOW</div>
                                        </div>
                                        <img class="img-fluid-book" src="{{Storage::url($roomtype->image)}}" alt="..." />
                                    </a>
                                @else
                                    <a class="portfolio-link">
                                        <div class="portfolio-hover">
                                            <div class="portfolio-hover-content">BOOK NOW</div>
                                        </div>
                                        <img class="img-fluid-book" src="{{Storage::url($roomtype->image)}}" alt="..." />
                                    </a>
                                @endif
                                <div class="portfolio-caption">
                                    <div class="portfolio-caption-heading">{{$roomtype->roomtype}}</div>
                                    @if($roomtype->available_rooms > 0)
                                        <div class="portfolio-caption-subheading text-dark" style="font-style: normal">Available Room: {{ $roomtype->available_rooms }}
                                        </div>
                                    @else
                                    <div class="portfolio-caption-subheading text-dark" style="font-style: normal">Available Room: No Available Room
                                    </div>
                                    @endif
                                    <div class="portfolio-caption-subheading text-dark" style="font-style: normal">Good For {{$roomtype->capacity}} Persons</div>
                                    <div class="portfolio-caption-subheading text-dark" style="font-style: normal">Per Night: &#8369;{{number_format($roomtype->price, 2, '.', ',')}}</div>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-end mt-2">
                        {{$roomtypes->links('vendor.livewire.bootstrap')}}
                    </div>
                @endif
            </div>
        </div>
    </section>

    @livewire('room-booking-modal')
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
    window.addEventListener('refreshSelectedRoomIds', (selectedRoomIds) => {
        Livewire.dispatch('updateSelectedRoomIds', selectedRoomIds);
    });
</script>

<script>
    window.addEventListener('close-modal', event =>{
        $('#createModal').modal('hide');
    });

    window.addEventListener('show-booking-modal', event =>{
        $('#createModal').modal('show');
    });
</script>
@endsection
