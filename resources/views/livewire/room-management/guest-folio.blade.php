<div wire:ignore.self class="modal fade" id="guestFolio" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg tableToHideInPrint" role="document" >
        <div class="modal-content">
            <div class="modal-header" style="background-color: #191c24">
                <h5 class="modal-title">Guest Check Out</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #191c24">

                {{-- 1st container start --}}
                <div class="container">
                    {{-- <div class="d-flex">
                        <div class="container mt-4 col-8 ml-0">
                            <h3 class="text-uppercase">Trigo Hotel</h3>
                        </div>
                        <div class="container col-4 mr-0 text-right">
                            <p class="text-small mb-1">Date: November 10, 2023 3:00 PM</p>
                            <p class="text-small mb-1">Receptionist: Liz Park</p>
                            <p class="text-small mb-1">Invoice #: 0000123</p>
                        </div>
                    </div> --}}

                    <div class="d-flex">
                        <div class="container mt-4 col-8 ml-0">
                            <h3 class="text-uppercase">{{auth()->user()->hotel_name}}</h3>
                        </div>
                        <div class="container col-4 mr-0" style="margin-left: -280px;">
                            <p class="text-small mb-1" >Date: {{$date_today}}</p>
                            <p class="text-small mb-1" style="margin-left: 58px;">Receptionist: {{auth()->user()->name}}</p>
                            <p class="text-small mb-1" style="margin-left: 70px;">Invoice #: {{$invoice_no}}</p>
                        </div>
                    </div>
        
                    <div class="container ml-0 mt-3">
                        <p class="text-small mb-1 font-weight-bold">Hotel Name: {{auth()->user()->hotel_name}}</p>
                        <p class="text-small mb-1">Email Address: hotel.ms.simulator@gmail.com</p>
                        <p class="text-small mb-1">Address: Marcelo, M.H Del Pilar St, Tagoloan, Misamis Oriental</p>
                    </div>

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
                                        <td>{{$b_data['room']}}</td>
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
                        
                        <div class="container mt-4">
                            <h4>Dining Charges</h4>
                        </div>
                        
                        <div class="container mt-2">
                            
                            <table class="table table-bordered">
                                <thead class="bg-primary">
                                    <tr>
                                        <th class="col-6">Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Date Purchase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($dining_data) > 0)
                                            @php
                                                $prevOrderId = null;
                                            @endphp
                                        
                                        @foreach (collect($dining_data)->groupBy('order_id') as $d_items)
                                            @foreach ($d_items as $item)
                                                @if($item['order']['id'] !== $prevOrderId)
                                                    <tr>
                                                        <td colspan="5">
                                                            @if(isset($item['order']) && $item['order'] && $item['order']['created_at'])
                                                                @if($item['order']['created_at']->hour >= 5 && $item['order']['created_at']->hour < 12)
                                                                    <h4>Breakfast</h4>
                                                                @elseif ($item['order']['created_at']->hour >= 12 && $item['order']['created_at']->hour < 18)
                                                                    <h4>Lunch</h4>
                                                                @else
                                                                    <h4>Dinner</h4>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                        
                                                <tr>
                                                    <td>{{$item['product_name']}}</td>
                                                    <td>&#8369;{{number_format($item['price'], 2, '.',',')}}</td>
                                                    <td>{{$item['quantity']}}</td>
                                                    <td>&#8369;{{number_format($item['total'], 2, '.',',')}}</td>
                                                    <td>{{$item['date_created']}}</td>
                                                </tr>
                                        
                                                @php
                                                    $prevOrderId = $item['order']['id'];
                                                @endphp
                                            @endforeach
                                        @endforeach
                                
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="5">No record found</td>
                                        </tr>
                                    @endif
                                        <tr class="table-bordered">
                                            <td colspan="3"></td>
                                            <td class="text-uppercase font-weight-bold">Sub Total</td>
                                            <td class="font-weight-bold">&#8369;{{number_format($dine_sub_total, 2, '.',',')}}</td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>

                    <div class="container mt-2">
                        <table class="table table-borderless col-3 mt-0">
                            <tbody>
                                <tr>
                                    <td class="p-2">Booking Sub Total:</td>
                                    <td class="p-2" >&#8369;{{number_format($book_sub_total, 2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td class="p-2" >Dining Sub Total:</td>
                                    <td class="p-2" >&#8369;{{number_format($dine_sub_total, 2,'.',',')}}</td>
                                </tr>
                                {{-- <tr>
                                    <td class="p-2" >Tax(12%):</td>
                                    <td class="p-2" >500</td>
                                </tr> --}}
                                <tr class="mt-2">
                                    <td class="p-2 text-uppercase" ><h4>Total Amount:</h4></td>
                                    <td class="p-2" ><h4>&#8369;{{number_format($total_amount, 2,'.',',')}}</h4></td>
                                </tr>
                            </tbody>
                        </table>
                </div>

                </div>
                {{-- 1st container end --}}

                    
        </div>
    </div>
</div>
</div>
