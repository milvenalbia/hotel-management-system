<div wire:ignore.self class="modal fade" id="checkInModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" >
        <div class="modal-content" @if($hide == true) style="opacity: 0%" @endif>
            <div class="modal-header" style="background-color: #191c24">
                <h5 class="modal-title">Check In</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #191c24">
                   <div class="d-flex justify-content-between">
                    <div class="cotainer ml-4">
                        <form wire:submit.prevent="submit">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="form-group w-100 mr-3">
                                    <label for="firstname">Firstname</label>
                                    <input wire:model.live.debounce.500ms="firstname" class="form-control" id="firstname" type="text" placeholder="Enter Firstname"/>
                                    @error('firstname')
                                    <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>
                                <div class="form-group w-100">
                                    <label for="lastname">Lastname</label>
                                    <input wire:model.live.debounce.500ms="lastname" class="form-control" id="lastname" type="text" placeholder="Enter lastname"/>
                                    @error('lastname')
                                    <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contact_no">Phone Number</label>
                                <input wire:model.live.debounce.500ms="contact_no" class="form-control" id="contact_no" type="number" placeholder="Enter Phone Number"  min="0"/>
                                @error('contact_no')
                                <span class="text-danger text-xs">{{$message}}</span> 
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email Address</label>
                                <input wire:model.live.debounce.500ms="email" class="form-control" id="email" type="email" placeholder="Enter Email Address"/>
                                @error('email')
                                <span class="text-danger text-xs">{{$message}}</span> 
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between mb-3 form-group">
                                <div class="form-group w-100 mr-3">
                                    <label for="check_in">Check In Date</label>
                                    <input wire:model.live.debounce.500ms="check_in" class="form-control" type="text" autocomplete="off" readonly/>
                                    @error('check_in')
                                    <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>

                                <div class="form-group w-100">
                                    <label for="check_out">Check Out Date</label>
                                    <input wire:model.live.debounce.500ms="check_out" class="form-control check_out" id="check_out" type="text" autocomplete="off" readonly/>
                                    @error('check_out')
                                    <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>
                                
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="form-group w-100 mr-3">
                                    {{-- @if($checked_button == true || $roomtype_id != $roomtype_fetch_id)
                                        @if($room_capacity == null)
                                            <label for="extra_bed">Extra Bed (Disabled)</label>
                                            <div class="input-group">
                                            <input wire:model.live.debounce.500ms="orig_extra_bed" class="form-control" id="orig_extra_bed" type="text" readonly/>

                                            <input wire:model.live.debounce.500ms="extra_bed" class="form-control" id="extra_bed" type="number"  min="0" placeholder="+ Extra Bed" readonly/>
                                            </div>
                                            @error('extra_bed')
                                            <span class="text-danger text-xs">{{$message}}</span> 
                                            @enderror
                                        @else
                                            <label for="extra_bed">Extra Bed</label>
                                            <div class="input-group">
                                            <input wire:model.live.debounce.500ms="orig_extra_bed" class="form-control" id="orig_extra_bed" type="text" readonly/>
                                            
                                            <input wire:model.live.debounce.500ms="extra_bed" class="form-control" id="extra_bed" type="number"  min="0" placeholder="+ Extra Bed"/>
                                            </div>
                                            @error('extra_bed')
                                            <span class="text-danger text-xs">{{$message}}</span> 
                                            @enderror
                                        @endif
                                    @else
                                        <label for="extra_bed">Extra Bed</label>
                                        <div class="input-group">
                                        <input wire:model.live.debounce.500ms="orig_extra_bed" class="form-control" id="orig_extra_bed" type="text" readonly/>
                                    
                                        <input wire:model.live.debounce.500ms="extra_bed" class="form-control" id="extra_bed" type="number"  min="0" placeholder="+ Extra Bed"/>
                                        </div>
                                        @error('extra_bed')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                        @enderror
                                    @endif --}}

                                    <label for="extra_bed">Extra Bed</label>
                                        <div class="input-group">
                                        <input wire:model.live.debounce.500ms="orig_extra_bed" class="form-control" id="orig_extra_bed" type="text" readonly/>

                                            <button wire:click="decrement('bed')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                                <input wire:model.live.debounce.500ms="extra_bed" class="form-control rounded-0 text-center" id="extra_bed" type="number"  min="0" autocomplete="off" placeholder="Extra Bed" readonly/>
                                            <button wire:click="increment('bed')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                        </div>
                                        @error('extra_bed')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror

                                </div>
                                <div class="form-group w-100">
                                    @if($checked_button == true || $roomtype_id != $roomtype_fetch_id)
                                        <label for="adult_no">No. of Room <span class="text-danger">*</span></label>
                                    @else
                                        <label for="adult_no">No. of Room</label>
                                    @endif
                                    <div class="input-group">
                                    <input wire:model.live="orig_room_no" class="form-control" id="orig_room_no" type="text" readonly/>
                                    {{-- <select wire:model.live="room_no_method" class="bg-light">
                                        <option value="plus">+</option>
                                        <option value="minus">-</option>
                                    </select> --}}
                                    <button wire:click="decrement('room')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                                <input wire:model.live.debounce.500ms="room_no" class="form-control text-center" id="room_no" type="number" autocomplete="off" min="0" placeholder="No. of Room" readonly/>
                                            <button wire:click="increment('room')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                        </div>
                                        @error('room_no')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                        @enderror
                                </div>
                                
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="form-group w-100 mr-3">
                                    @if($checked_button == true || $roomtype_id != $roomtype_fetch_id)
                                        <label for="adult_no">Adult <span class="text-danger">*</span></label>
                                    @else
                                        <label for="adult_no">Adult</label>
                                    @endif
                                    <div class="input-group">
                                    <input wire:model.live.debounce.500ms="orig_adult_no" class="form-control" id="orig_adult_no" type="text" readonly/>
                                    {{-- <select wire:model.live="adult_no_method" class="bg-light">
                                        <option value="plus">+</option>
                                        <option value="minus">-</option>
                                    </select> --}}
                                    <button class="btn rounded-0" type="button" style="background: #2A3038;" wire:click="decrement('adult')"><i class="mdi mdi-minus"></i></button>
                                                <input wire:model.live.debounce.500ms="adult_no" class="form-control text-center" id="adult_no" type="number" autocomplete="off" min="0" placeholder="Adult No." readonly/>
                                            <button class="btn rounded-0" type="button" style="background: #2A3038;" wire:click="increment('adult')"><i class="mdi mdi-plus"></i></button>
                                        </div>
                                        @error('adult_no')
                                                <span class="text-danger text-xs">{{$message}}</span> 
                                            @enderror
                                </div>
                                <div class="form-group w-100 mb-3">
                                    <label for="children_no">Children (Below 11 Years Old)</label>
                                    <div class="input-group">
                                    <input wire:model.live.debounce.500ms="orig_children_no" class="form-control" id="orig_children_no" type="text" readonly/>
                                    {{-- <select wire:model.live="children_no_method" class="bg-light">
                                        <option value="plus">+</option>
                                        <option value="minus">-</option>
                                    </select> --}}
                                    <button wire:click="decrement('child')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                                <input wire:model.live.debounce.500ms="children_no" class="form-control text-center" id="children_no" type="number" autocomplete="off" min="0" placeholder="Children No." readonly/>
                                                @error('children_no')
                                                <span class="text-danger text-xs">{{$message}}</span> 
                                                @enderror
                                            <button wire:click="increment('child')" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                        </div>
                                        
                                        @error('children_no')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                        @enderror
                                </div>
                            </div>
                            @error('total_guest') <span class="text-danger text-xs">{{ $message }}</span> @enderror

                            {{-- @if($children_no > 0)
                            <div class="children-container">
                                <p class="text-md">Age is a required field. Kindly make a selection.</p>
                                @for($i = 1; $i <= $children_no; $i++)
                                    @if($i % 3 == 1)
                                        <div class="row mb-3">
                                    @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_ages{{ $i }}">Child {{ $i }} Age <span class="text-danger">*</span></label>
                                            <select wire:model.live.debounce.300ms="child_ages.{{$i}}" class="form-control border @if(empty($child_ages[$i])) border-danger @else border-success @endif">
                                                <option value="">Select Age</option>
                                                <option value="1">1 years old</option>
                                                <option value="2">2 years old</option>
                                                <option value="3">3 years old</option>
                                                <option value="4">4 years old</option>
                                                <option value="5">5 years old</option>
                                                <option value="6">6 years old</option>
                                                <option value="7">7 years old</option>
                                                <option value="8">8 years old</option>
                                                <option value="9">9 years old</option>
                                                <option value="10">10 years old</option>
                                                <option value="11">11 years old</option>
                                                <option value="12">12 years old</option>
                                                <option value="13">13 years old</option>
                                                <option value="14">14 years old</option>
                                                <option value="15">15 years old</option>
                                                <option value="16">16 years old</option>
                                                <option value="17">17 years old</option>
                                            </select>
                                        </div>
                                        @error('child_ages.' . $i) <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                    </div>
                                    @if($i % 3 == 0 || $i == $children_no)
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        @endif --}}

                        @if($roomtype_id == $roomtype_fetch_id)
                        <div class="form-check form-check-flat form-check-primary">
                            <label class="form-check-label text-light mb-3">
                            <input type="checkbox" class="form-check-input" id="check_button" wire:model.live="checked_button">Reset original: (Extra Bed; Rooms; Adult; & Children)<i class="input-helper"></i></label>
                        </div>
                        @endif
                        @if(!$checked_button)
                            <div class="form-group">
                                <label for="roomtype_id">Room Type</label>
                                <select class="form-control" wire:model.live="roomtype_id" id="roomtype_id" wire:change="changeRoomTypes">
                                    @if($roomTypes)
                                    @foreach($roomTypes as $roomtype)
                                        <option value="{{ $roomtype->id }}">{{ $roomtype->roomtype }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('roomtype_id') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="form-group">
                                <label for="roomtype_id">Room Type (Read Only)</label>
                                <select class="form-control" wire:model.live="roomtype_id" id="roomtype_id" wire:change="changeRoomTypes" disabled>
                                    @if($roomTypes)
                                    @foreach($roomTypes as $roomtype)
                                        <option value="{{ $roomtype->id }}">{{ $roomtype->roomtype }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('roomtype_id') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                        @endif
                            @if($room_no > 0)
                            <div class="room-container">
                                @for($i = 1; $i <= $room_no; $i++)
                                        <div class="form-group">
                                            <label for="room_id{{ $i }}">Select Room Number {{ $i }} <span class="text-danger">*</span></label>
                                            <select wire:model.live.debounce.300ms="room_id.{{ $i }}" class="form-control border @if(empty($room_id[$i]) || $errors->has("room_id")) border-danger @else border-success @endif" @if(empty($check_in)) disabled title="Fill up check-in date first" @endif>
                                                <option value="">Select Room Number</option>
                                                @if($availableRooms)
                                                    @foreach($availableRooms as $room)
                                                        <option value="{{ $room->id }}">{{ $room->room_no }} &nbsp;(+ {{$room->extra_bed}} Extra Bed)</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        
                                @endfor

                            </div>
                            @error('room_id') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        @endif
                        <table class="table mt-3">
                            <tbody>
                                <tr>
                                    <td>Added Cost (Extra Bed):</td>
                                    <td>&#8369;{{number_format($extraBedPrice, 2, '.', ',')}}</td>
                                </tr>
                                <tr>
                                    <td>Total Amount:</td>
                                    <td>&#8369;{{number_format($roomPrice, 2, '.', ',')}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-group my-3">
                            <label for="advance_payment">Advance Payment<span class="text-danger">*</span></label>
                            <input wire:model.live.debounce.500ms="advance_payment" class="form-control" id="advancePayment" type="number" min="0" step="0.01" placeholder="Enter Advance Payment"/>
                            @error('advance_payment')
                            <span class="text-danger text-xs">{{$message}}</span> 
                            @enderror
                        </div>

                        @if($payment_method == 'Cash' || $advance_payment > $roomPrice)
                            <div class="form-group">
                                <label for="change" class="text-uppercase">change:</label>
                                <input type="number" class="form-control" wire:model.live.debounce.300ms="change" readonly>
                                @error('change')
                                <span class="text-danger text-xs">{{$message}}</span> 
                                @enderror
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="payment_method" class="text-uppercase">Payment Method</label>
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

                        {{-- @if($checked_button == true || $roomtype_id != $roomtype_fetch_id)
                            @if($children_no > 0 && $room_no == '')
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_id) || is_null($advance_payment) || in_array('', $child_ages) ? 'disabled' : '' }}>Proceed Check In</button>
                            @elseif($children_no == 0 && $room_no > 0)
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) ? 'disabled' : '' }}>Proceed Check In</button>
                            @elseif($children_no > 0 && $room_no > 0)
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id)|| in_array('', $child_ages) ? 'disabled' : '' }}>Proceed Check In</button>
                            @else
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_no) || is_null($adult_no) || $adult_no == 0 || $room_no == 0 ? 'disabled' : '' }}
                                >Proceed Check In</button>
                            @endif
                        @else
                            @if($children_no > 0 && $room_no == '')
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_id) || in_array('', $child_ages) ? 'disabled' : '' }}>Proceed Check In</button>
                            @elseif($children_no == 0 && $room_no > 0)
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) ? 'disabled' : '' }}>Proceed Check In</button>
                            @elseif($children_no > 0 && $room_no > 0)
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id)|| in_array('', $child_ages) ? 'disabled' : '' }}>Proceed Check In</button>
                            @else
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) ? 'disabled' : '' }}
                                >Proceed Check In</button>
                            @endif
                        @endif --}}

                        @if($checked_button == true || $roomtype_id != $roomtype_fetch_id)
                            @if($room_no > 0)
                                @if($payment_method == 'Paypal')
                                    <button type="button" wire:click="payWithPaypal" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>

                                @elseif($payment_method == 'Cash')
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>

                                @elseif($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                                    <button type="button" wire:click="payWithCard"  class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>

                                @else
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>
                                @endif
                            @else
                                @if($payment_method == 'Paypal')
                                    <button type="button" wire:click="payWithPaypal" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_no) || is_null($adult_no) || $adult_no == 0 || $room_no == 0 ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @elseif($payment_method == 'Cash')
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_no) || is_null($adult_no) || $adult_no == 0 || $room_no == 0 ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @elseif($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                                    <button type="button" wire:click="payWithCard" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($room_no) || is_null($adult_no) || $adult_no == 0 || $room_no == 0 ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @else
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>
                                @endif
                                
                            @endif
                        @else
                            @if($room_no > 0)
                                @if($payment_method == 'Paypal')
                                    <button type="button" wire:click="payWithPaypal" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) ? 'disabled' : '' }}>Proceed Check In</button>

                                @elseif($payment_method == 'Cash')
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) ? 'disabled' : '' }}>Proceed Check In</button>

                                @elseif($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                                    <button type="button" wire:click="payWithCard"  class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) ? 'disabled' : '' }}>Proceed Check In</button>
                                @else
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>
                                @endif
                            @else
                                @if($payment_method == 'Paypal')
                                    <button type="button" wire:click="payWithPaypal" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($payment_method) ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @elseif($payment_method == 'Cash')
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($payment_method) ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @elseif($payment_method == 'Credit Card' || $payment_method == 'Debit Card')
                                    <button type="button" wire:click="payWithCard" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($advance_payment) || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || is_null($payment_method) ? 'disabled' : '' }}
                                    >Proceed Check In</button>

                                @else
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 mt-4"
                                    {{ $errors->any() || is_null($firstname) || is_null($lastname) || is_null($contact_no) || is_null($email) || is_null($advance_payment) || is_null($check_in) || is_null($check_out) || is_null($roomtype_id) || in_array('', $room_id) || is_null($payment_method) ? 'disabled' : '' }}>Proceed Check In</button>
                                @endif
                                
                            @endif
                        @endif

                        </form>
                    </div>
                        <div class="container col-5">
                        @if($roomtypes)
                            <img class="img-fluid-book" src="{{Storage::url($roomtypes->image)}}" alt="">
                            <h4 class=" my-3 text-uppercase">Roomtype: {{$roomtypes->roomtype}}</h4>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Per Nights</td>
                                        <td>&#8369;{{number_format($roomtypes->price, 2,'.',',')}}</td>
                                    </tr>
                                    <tr>
                                        <td>Good for </td>
                                        <td>{{$roomtypes->capacity}} Persons</td>
                                    </tr>

                                    <tr>
                                        <td>Available Extra Bed</td>
                                        @if($room_capacity > 0 || $orig_room_capacity > 0)
                                        <td>{{(int)$room_capacity + (int)$orig_room_capacity}}</td>
                                        @endif
                                        
                                    </tr>
                        @endif
                                    <tr>
                                        <td><h6 class="my-1 text-uppercase">Booking Info:</h6></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Number of Nights</td>
                                        @if(empty($nights))
                                        <td>{{$orig_nights}}</td>
                                        @else
                                        <td>{{$nights}}</td>
                                        @endif
                                    </tr>
                                    {{-- <tr>
                                        <td>Counted Guest</td>
                                        @if(empty($total_guest))
                                        <td>{{$orig_total_guest}}</td>
                                        @else
                                        <td>{{$total_guest}}</td>
                                        @endif
                                    </tr> --}}
                                    <tr>
                                        <td>Total Capacity</td>
                                        @if(!empty($extra_bed) || !empty($room_no))
                                        <td>{{$total_capacity}}</td>
                                        @else
                                        <td>{{$orig_total_capacity}}</td>
                                        @endif
                                        
                                    </tr>
                                    <tr>
                                        <td><h6 class="my-1 text-uppercase text-spacing-1">House Rules:</h6></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Check In Time</td>
                                        <td>2:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Check Out Time</td>
                                        <td>12:00 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Pets are not allowed!</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                   </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="roomtypeModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-light" style="background-color: #ffffff;">
                <div class="modal-header border-light">
                    <h5 class="modal-title text-dark" id="conflictModalLabel">Notification</h5>
                    </button>
                </div>
                <div class="modal-body text-center text-dark">
                    There are no available rooms for the selected room type.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-md btn-primary py-2 px-4" wire:click="closeConflictModal">Ok</button>
                </div>
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
                    <div style="background-image: url(https://www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg);width: 246px;
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
                        @if($advance_payment > 0)
                        <p class="text-small text-dark"><i class="mdi mdi-cart mr-2"></i>&#8369;{{number_format($advance_payment, 2,'.',',')}}</p>
                        @endif
                    </div>
                    <p class="text-small pl-2 py-3 mb-2 mt-2 bg-primary text-light">Hi, {{$firstname}}</p>
                    <h5>Ship to</h5>
                    <p class="text-small text-dark font-weight-bold">{{$guest_name}}</p>
                    <p class="text-xs mt-0">{{auth()->user()->hotel_name}}</p>
                    <p class="text-primary text-small mt-0">Change</p>
                    @if($advance_payment > $roomPrice)
                        <p class="text-small text-dark"><i class="mdi mdi-cart mr-2"></i>&#8369;{{number_format($change, 2,'.',',')}}</p>
                    @endif
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
@if($payment_method == 'Credit Card' || $payment_method == 'Debit Card')

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
                    <h4>Transaction Amount:</h4>
                    @if($advance_payment > 0)
                    <h4 class="bg-primary p-2 my-2 text-light">&#8369;{{number_format($advance_payment, 2,'.',',')}}</h4>
                    @endif
                    @if($advance_payment > $roomPrice)
                    <h6 class="bg-primary p-2 my-2 text-light">Change: &#8369;{{number_format($change, 2,'.',',')}}</h6>
                    @endif
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

@endif
</div>
@section('check-in-scripts')
<script>
    document.addEventListener('livewire:init', function () {
        const firstnameInput = document.getElementById('firstname');
        const lastnameInput = document.getElementById('lastname');
        const advancePaymentInput = document.getElementById('advancePayment');

        firstnameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        lastnameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        advancePaymentInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^0-9.]/g, '')
        });
    });
</script>
@endsection