<div wire:ignore.self class="modal fade" id="viewGuestModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header" style="background-color: #191c24">
                <h5 class="modal-title">Guest Booking Details</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #191c24">
                <div class="container ml-0 mt-2">
                    <p class="w-100 bg-secondary text-large text-dark text-center text-uppercase p-2"><strong>Booking Information</strong></p>
                        <table class="table table-borderless col-3 mt-0">
                            <tbody>
                                @foreach($booking_data as $b_data)
                                <tr>
                                    <td class="p-2">Guest Folio #:</td>
                                    <td class="p-2" >{{$b_data['folio']}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2">Name:</td>
                                    <td class="p-2" >{{$b_data['firstname']}} {{$b_data['lastname']}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Phone #:</td>
                                    <td class="p-2" >{{$b_data['phone']}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Email Address:</td>
                                    <td class="p-2" >{{$b_data['email']}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Room Type:</td>
                                    <td class="p-2" >{{$b_data['roomtype']}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Check In Date:</td>
                                    <td class="p-2" >{{$check_in}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Check Out Date:</td>
                                    <td class="p-2" >{{$check_out}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>

                <div class="container ml-0 mt-3">
                    <table class="table table-bordered">
                        <thead class="bg-secondary text-dark">
                            <tr class="font-weight-bold">
                                <td class="col-6">Description</td>
                                <td>Quantity</td>
                                <td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking_data as $b_data)
                            <tr>
                                <td>
                                    <p class="text-small text-ligt">Rooms:</p>
                                    @if(count($room_no) > 1)
                                        @foreach($room_no as $rooms)
                                            <p class="text-small text-ligt">Room Number: {{$rooms->room_no}}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    {{$b_data['room']}}
                                </td>
                                <td>&#8369;{{number_format($room_price, 2, '.', ',')}}</td>
                               
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Additional Charges:</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="py-2" >Extra Bed</td>
                                <td class="py-2" >{{$b_data['extra_bed']}}</td>
                                <td class="py-2" >&#8369;{{number_format($b_data['extra_bed_amount'], 2, '.',',')}}</td>
                            </tr>
                            <tr>
                                <td class="py-2" >Extend Hours</td>
                                <td class="py-2" >{{$b_data['extend_hours']}}</td>
                                <td class="py-2" >&#8369;{{number_format($extend_hours_amount, 2, '.', ',')}}</td>
                            </tr>
                            <tr> 
                                <td class="py-2" >Extend Days</td>
                                <td class="py-2" >{{$b_data['extend_days']}}</td>
                                <td class="py-2" >&#8369;{{number_format($extend_days_amount, 2, '.',',')}}</td>
                            </tr>
                            <tr>
                                <td>Nights of Stay</td>
                                <td>{{$nights}}</td>
                                <td>&#8369;{{number_format($nights_amount, 2, '.',',')}}</td>
                            </tr>
                            <tr>
                                <td>Total Stay</td>
                                <td>
                                    @if($total_nights > 1)
                                        {{$total_nights}} Nights
                                    @else
                                        {{$total_nights}} Night
                                    @endif
                                </td>
                                <td>&#8369;{{number_format($total_nights_amount, 2, '.',',')}}</td>
                            </tr>
                            <tr>
                                <th class="col-6"></th>
                                <th class="col-3 text-uppercase">Sub total</th>
                                <th class="col-3">&#8369;{{number_format($book_sub_total, 2, '.',',')}}</th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>