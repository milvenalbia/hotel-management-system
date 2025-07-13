<div wire:ignore.self class="modal fade" id="viewRoomHistory" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header" style="background-color: #191c24">
                <h5 class="modal-title">Room Guest History</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #191c24">
                    <div class="d-flex justify-content-between my-3">
                        <h4 class="text-lightmt-2">Guest List</h4>
                        <select class="form-control text-white mr-2 bg-light text-dark" wire:model.live.debounce.200ms="by_date" style="width: 20%;">
                            <option value="all">All</option>
                            <option value="today">Today</option>
                            <option value="weekly">This Week</option> 
                            <option value="monthly">This Month</option>
                            <option value="yearly">This Year</option>
                        </select>
                    </div>

                        <table class="table table-bordered table-hover text-light">
                            <thead>
                                <tr>
                                    <th class="text-light">Guest Folio #</th>
                                    <th class="text-light">Name</th>
                                    <th class="text-light">Email Address</th>
                                    <th class="text-light">Check In Date</th>
                                    <th class="text-light">Check Out Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bookingTransaction)
                                @if(count($guest_data) > 0)
                                @foreach($guest_data as $d => $guest)
                                <tr wire:click="openFolio({{$guest['id']}})" style="cursor: pointer;"> 
                                    <td>{{$guest['folio']}}</td>
                                    <td>{{$guest['name']}}</td>
                                    <td>{{$guest['email']}}</td>
                                    <td>{{$guest['check_in']}}</td>
                                    <td>{{$guest['check_out']}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="7">No guest record is found.</td>
                                </tr>
                                @endif
                                @endif
                            </tbody>
                        </table>
 
    
            </div>
        </div>
    </div>
</div>
