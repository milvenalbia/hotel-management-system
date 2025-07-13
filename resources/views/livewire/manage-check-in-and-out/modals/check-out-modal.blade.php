<div>
<div wire:ignore.self class="modal fade" id="checkOutModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable tableToHideInPrint" role="document" >
        <div class="modal-content" @if($hide == true) style="opacity: 0%" @endif>
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

                    <div class="container ml-0 mt-3">
                        @foreach($user_data as $data)
                        <p class="text-small mb-1"><strong>{{$data['hotel_name']}}</strong></p>
                        @endforeach
                        <p class="text-small mb-1">hotel.ms.simulator@gmail.com</p>
                        <p class="text-small mb-1">Marcelo, M.H Del Pilar St, Tagoloan, Misamis Oriental</p>
                    </div>

                        <div class="container ml-0 mt-2">
                            <p class="w-100 bg-secondary text-large text-dark text-center text-uppercase p-2"><strong>Booking Information</strong></p>
                                <table class="table table-borderless col-3 mt-0">
                                    <tbody>
                                        @foreach($booking_data as $b_data)
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
                                        <td>Room</td>
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
                                <tr>
                                    <td class="p-2" >Advance Payment:</td>
                                    <td class="p-2" >&#8369;{{number_format($advance_payment, 2,'.',',')}}</td>
                                </tr>
                                <tr class="mt-2">
                                    <td class="p-2 text-uppercase" ><h4>Remaining Balance:</h4></td>
                                    <td class="p-2" ><h4>&#8369;{{number_format($remaining_amount, 2,'.',',')}}</h4></td>
                                </tr>
                            </tbody>
                        </table>
                </div>


                <div class="text-dark d-flex mx-3" style="margin-bottom: 375px;">
                    <div class="card col-12">
                        <div class="card-header w-100 m-1">
                            <h4 class="text-uppercase">Check out Payment</h4>
                        </div>
                        <div class="card-body">
                    <form class="ml-0" wire:submit.prevent="submit">
                        
                        <div class="form-group">
                            <label for="payment_method" class="text-uppercase font-weight-bold">Payment Method</label>
                            <select class="form-control" wire:model.live.debounce.300ms="payment_method">
                                <option value="">Select Payment Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Paypal">Paypal</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                            </select>
                            @error('payment_method')
                            <span class="text-danger text-xs">{{$message}}</span> 
                            @enderror
                        </div>
                        @if($payment_method == 'Cash')
                            <div class="form-group">
                                <label for="payment" class="text-uppercase">Payment:</label>
                                <input type="number" class="form-control" wire:model.live.debounce.300ms="payment" placeholder="Please Enter Payment">
                                @error('payment')
                                <span class="text-danger text-xs">{{$message}}</span> 
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="change" class="text-uppercase">change:</label>
                                <input type="number" class="form-control" wire:model.live.debounce.300ms="change" readonly>
                                @error('change')
                                <span class="text-danger text-xs">{{$message}}</span> 
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" {{ $errors->any() || is_null($payment) ? 'disabled' : '' }}>Proceed Check Out</button>
                            </div>
                        @elseif($hideButton == false)
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary"
                                {{ $errors->any() || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check out</button>
                            </div>
                        @endif
                        
                    </form>

                    @if($payment_method == 'Paypal')
                            <div class="d-flex justify-content-end">
                                <button type="submit" wire:click="payWithPaypal" class="btn btn-primary">Proceed Check out</button>
                            </div>
                    @elseif($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                        <div class="d-flex justify-content-end">
                            <button type="submit" wire:click="payWithCard" class="btn btn-primary">Proceed Check out</button>
                        </div>
                    @endif

                    </div>
                </div>
                </div>

                </div>
                {{-- 1st container end --}}

                    
        </div>
    </div>
</div>

{{-- Check Out Receipt --}}
<div class="container" id="printCheckOut">
    {{-- <div class="container mt-5">
        <div class="d-flex justify-content-center">
            <img src="{{ Storage::url(auth()->user()->logo) }}" class="img-fluid mr-3" alt="">
            <h4 class="mt-5 mx-2">Hotel Management Sytem Simulator</h4>
            <img src="{{ Storage::url(auth()->user()->logo) }}" class="img-md ml-3" alt="">
        </div>
    </div> --}}
        <div class="d-flex">
            <div class="container mt-4 col-8 ml-0">
                <h3 class="text-uppercase">{{auth()->user()->hotel_name}} Invoice</h3>
            </div>
            <div class="container col-4 mr-0" style="margin-left: -280px;">
                <p class="text-small mb-1" >Date: {{$date_today}}</p>
                <p class="text-small mb-1" style="margin-left: 57px;">Receptionist: {{auth()->user()->name}}</p>
                <p class="text-small mb-1" style="margin-left: 109px;">Invoice #: {{$invoice_no}}</p>
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
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking_data as $b_data)
                    <tr>
                        <td>Room</td>
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
                        <td></td>
                        <td class="text-uppercase font-weight-bold">Sub total</td>
                        <td class="font-weight-bold">&#8369;{{number_format($book_sub_total, 2, '.',',')}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="container mt-4">
            <h4>Dining Charges</h4>
            <table class="table table-bordered">
                <thead class="bg-primary">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Meal Time</th>
                        <th>Date Purchase</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($dining_data) > 0)
                            @php
                                $rowCount = 0;
                            @endphp

                            @foreach ($dining_data as $item)
                                @if($rowCount < 5)
                                    <tr>
                                        <td>{{$item['product_name']}}</td>
                                        <td>&#8369;{{number_format($item['price'], 2, '.',',')}}</td>
                                        <td>{{$item['quantity']}}</td>
                                        <td>&#8369;{{number_format($item['total'], 2, '.',',')}}</td>
                                        <td>
                                            @if(isset($item['order']) && $item['order'] && $item['order']['created_at'])
                                                @if($item['order']['created_at']->hour >= 5 && $item['order']['created_at']->hour < 12)
                                                    Breakfast
                                                @elseif ($item['order']['created_at']->hour >= 12 && $item['order']['created_at']->hour < 18)
                                                    Lunch
                                                @else
                                                    Dinner
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{$item['date_created']}}</td>
                                    </tr>
                                    @php
                                        $rowCount++;
                                    @endphp
                                @endif
                            @endforeach

                            @if(count($dining_data) > 5)
                                <tr>
                                    <td class="text-right" colspan="6">See more details in your email...</td>
                                </tr>
                            @endif

                        @else
                            <tr>
                                <td class="text-center" colspan="6">No guest order data is found.</td>
                            </tr>
                        @endif
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-uppercase font-weight-bold">Sub Total</td>
                        <td class="font-weight-bold">&#8369;{{number_format($dine_sub_total, 2, '.',',')}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="container mt-2">
            <table class="table table-borderless col-3 mt-0 page-break">
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
                    <tr>
                        <td class="p-2 text-uppercase" >Total Paid:</td>
                        <td class="p-2" >&#8369;{{number_format($paid_amount, 2,'.',',')}}</td>
                    </tr>
                    @if($payment_method == 'Cash')
                        <tr>
                            <td class="p-2 text-uppercase" >Change:</td>
                            <td class="p-2" >&#8369;{{number_format($change, 2,'.',',')}}</td>
                        </tr>
                    @endif
                    <tr class="mt-2">
                        <td class="p-2 text-uppercase" ><h4>Total Amount:</h4></td>
                        <td class="p-2" ><h4>&#8369;{{number_format($total_amount, 2,'.',',')}}</h4></td>
                    </tr>
                </tbody>
            </table>
        
        <div class="container d-flex justify-content-center my-3">
            <h4 class="col-10 text-dark text-center">
                " Thank you for choosing {{auth()->user()->hotel_name}}. We hope your stay was enjoyable. Enclosed is your detailed invoice.
                Your satisfaction is our priority. If you have any feedback or suggestions, please let us know. We look forward to welcome you back in the future. "
            </h4>
        </div>

        <div class="footer d-flex justify-content-center">
            <p class="text-small text-dark">&copy; 2023 {{auth()->user()->hotel_name}} & Resort. All rights reserved.</p>
        </div>
    </div>
</div>

@if($payment_method == 'Paypal')

{{-- Paypal Modal --}}
<div wire:ignore.self class="modal fade" id="paypalModal" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-dark" role="document">
        <div class="modal-content" @if($show == false) style="opacity: 0%" @endif>
            <div class="modal-header" style="background-color: #e8e9ec">
                <h5 class="modal-title text-dark" id="deleteModalLabel" >Pay with Paypal</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" wire:click="cancel()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-dark" style="background-color: #e8e9ec">
                <div class="container">
                    <div style="background-image: url(https://www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg);
                    width: 246px;
                    height: 60px;
                    background-size: cover;
                    background-position: left;
                    background-clip: content-box;
                    background-origin: content-box;
                    margin-left: 100px;"></div>

                    <form action="" wire:submit.prevent="loginPaypal" class="mt-5">
                        <div class="form-group">
                            <label class="text-left">Username</label>
                            <input type="text" wire:model.live.debounce="username" class="form-control rounded" style="height: 50px;">
                            @error('username')
                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="form-group">
                            <label class="text-left">Password</label>
                            <input type="password" wire:model.live.debounce="password" class="form-control rounded" style="height: 50px;">
                            @error('password')
                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                          </div>
                          <button type="submit" class="mt-2 btn btn-primary w-100 rounded-pill" style="height: 50px;">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div wire:ignore.self class="modal fade" id="paypalPaymentModal" tabindex="-9999"  data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-dark" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #e8e9ec">
                <h5 class="modal-title text-dark" id="deleteModalLabel">Pay with Paypal</h5>
                <button type="button" class="close text-danger mr-1 pt-4" wire:click="cancel()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-dark" style="background-color: #e8e9ec">

                <div class="container">
                    <div style="background-image: url(https://www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg);width: 246px;
                    height: 60px;
                    background-size: cover;
                    background-position: left;
                    background-clip: content-box;
                    background-origin: content-box;
                    margin-left: 100px;"></div>

                    <div class="d-flex justify-content-between mt-5">
                        <div style="background: transparent url(https://www.paypalobjects.com/paypal-ui/logos/svg/paypal-mark-color.svg) top center no-repeat;
                        width: 34px;
                        height: 40px;
                        display: block;">
                        </div>
                        <p class="text-small text-dark"><i class="mdi mdi-cart mr-2"></i>&#8369;{{number_format($payment, 2,'.',',')}}</p>
                    </div>
                    <p class="text-small pl-2 py-3 mb-2 mt-2 bg-primary text-light">Hi, {{$firstname}}</p>
                    <h5>Ship to</h5>
                    <p class="text-small text-dark font-weight-bold">{{$guest_name}}</p>
                    <p class="text-xs mt-0">{{auth()->user()->hotel_name}}</p>
                    <p class="text-primary text-small mt-0">Change</p>
                    <hr class="my-2">
                    <p class="text-dark text-small mb-2">Pay With</p>
                    <p class="text-primary text-small mb-2"><i class="mdi mdi-check-circle mr-2"></i>Paypal Balance</p>
                    <button class="mt-2 btn btn-primary w-100 rounded-pill" wire:click="submit" style="height: 40px;">Continue</button>
                </div>
                
            </div>
        </div>
    </div>
</div>

@endif
{{-- Paypal Modal End --}}
{{-- Credit Card or Debit Card Modal --}}

<div wire:ignore.self class="modal fade" id="cardModal" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-dark" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #e8e9ec">
                <h5 class="modal-title text-dark" id="deleteModalLabel">Pay with Card</h5>
                <button type="button" class="close text-danger mr-1 pt-4" wire:click="cancel()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center text-dark" style="background-color: #e8e9ec">
                @if($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                    <h4>Remaining Balance:</h4>
                    <h4 class="bg-primary p-2 my-2 text-light">&#8369;{{number_format($payment, 2,'.',',')}}</h4>
                    <p class="text-small">Click continue to proceed with the transaction.</p>
                @endif
            </div>
            <div class="modal-footer" style="background-color: #e8e9ec">
                <button type="button" class="btn btn-dark" wire:click="cancel()" data-dismiss="modal">No</button>
                <button class="btn btn-success" wire:click="submit">Continue</button>
            </div>
        </div>
    </div>
</div>
</div>