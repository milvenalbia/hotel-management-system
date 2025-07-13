
<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card bg-dark">
                <div class="card-body py-0 px-0 px-sm-3">
                  <div class="row align-items-center">
                    <div class="col-lg-2 col-sm-2 col-xl-2">
                      <img src={{ asset("admin/assets/images/dashboard/Group126@2x.png")}} class="img-fluid" alt="">
                    </div>
                    <div class="col-lg-4 col-sm-4 col-xl-4 pr-2">
                      <h3 class="mb-1 mb-sm-0">Room Management</h3>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xl-6 pl-0">
                        <input wire:model.live.debounce.200ms="search" class="form-control text-dark bg-white border-0 shadow rounded-pill" placeholder="Search ...">
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

    <div class="row">
        {{-- <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                  <h4 class="card-title text-dark mb-3">Create Room</h4>
                  <form wire:submit.prevent="submit" class="forms-sample">
                    <div class="form-group">
                        <label for="room_no" class="text-dark">Room Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-dark" id="room_no" wire:model.live.debounce.500ms="room_no"
                            placeholder="Room Number">
                    @error('room_no')
                    <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    </div>
                    <div class="form-group">
                        <label for="roomtype_id" class="text-dark">Select Room Type <span class="text-danger">*</span></label>
                        <select type="text" class="form-control bg-dark" id="roomtype_id" wire:model="roomtype_id">
                            <option value="0">Select Room Type</option>
                            @foreach($roomtypes as $roomtype)
                                <option value="{{$roomtype->id}}">{{$roomtype->roomtype}}</option>
                            @endforeach
                        </select>
                    @error('roomtype_id')
                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-primary"
                    {{ $errors->any() || is_null($room_no) || is_null($roomtype_id) ? 'disabled' : '' }}>Submit</button>
                </form>
                </div>
              </div>
        </div> --}}
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title text-dark">Room List</h4>
                        <div class="col-9 d-flex justify-content-end">
                        <select class="form-control text-white mr-2 bg-dark" wire:model.live="floor" style="width: 20%;">
                            <option value="">Filter by Floor</option>
                            <option value="1">1st Floor</option>
                            <option value="2">2nd Floor</option>
                            <option value="3">3rd Floor</option>
                            <option value="4">4th Floor</option>
                            <option value="5">5th Floor</option>
                            <option value="6">6th Floor</option>
                            <option value="7">7th Floor</option>
                            <option value="8">8th Floor</option>
                            <option value="9">9th Floor</option>
                        </select>
                        <select class="form-control text-white mr-2 bg-dark" wire:model.live="byRoomtype" style="width: 30%;">
                            <option value="">Filter by Roomtype</option>
                            @foreach($roomtypes as $roomtype)
                                <option value="{{$roomtype->id}}">{{$roomtype->roomtype}}</option>
                            @endforeach
                        </select>
                        <select class="form-control text-white mr-2 bg-dark" wire:model.live="status" style="width: 20%;">
                            <option value="">Filter by Status</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Vacant Ready">Vacant Ready</option>
                            <option value="Vacant Clean">Vacant Clean</option>
                            <option value="Vacant Dirty">Vacant Dirty</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Block">Block</option>
                        </select>
                        <button class="btn btn-primary mr-2" wire:click="openCreateRoomModal"><i class="mdi mdi-plus-circle mr-1"></i>New Room</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-dark">
                            <thead>
                                <tr>
                                  <th> # </th>
                                  <th> Room Type </th>
                                  <th> Room No.</th>
                                  <th> Capacity </th>
                                  <th> Room Price </th>
                                  <th> Room Status </th>
                                  <th> Action </th>
                                </tr>
                              </thead>
                              <tbody>
                                @if ($rooms->count() > 0)
                                    @foreach ($rooms as $d => $room)
                                    <tr>
                                        <td>{{++$d}}</td>
                                        <td>
                                            @if ($room->roomtypes)
                                            <img src="{{ Storage::url($room->roomtypes->image) }}" alt="Room Image" class="img-fluid mr-3">{{$room->roomtypes->roomtype }}
                                            @endif
                                        </td>
                                        <td>{{ $room->room_no }}</td>
                                        <td>{{ $room->roomtypes->capacity }} @if($room->extra_bed > 0) &nbsp; &nbsp; &nbsp; (+ {{$room->extra_bed}} Extra Bed) @endif</td>
                                        <td>&#8369;{{ number_format($room->roomtypes->price, 2, '.', ',') }}</td>
                                        <td>{{$room->room_status}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-md btn-primary" wire:click="editroom({{ $room->id }})">
                                                    <i class="mdi mdi-pencil-box-outline mr-2"></i>Edit
                                                </a>

                                                {{-- <a href="#" class="btn btn-md btn-danger" wire:click="deleteConfirmation({{ $room->id }})">
                                                    <i class="mdi mdi-delete mr-2"></i>Remove
                                                </a> --}}

                                                <a href="#" class="btn btn-md btn-info" wire:click="openHistory({{ $room->id }})">
                                                    <i class="mdi mdi-history mr-2"></i>Guest History
                                                </a>
                                            </div>
                                        </td>
                                        {{-- <td>

                                            <div class="d-flex">
                                                <a href="#" class="btn btn-sm btn-outline-primary mr-2" wire:click="editroom({{ $room->id }})">
                                                    <i class="mdi mdi-pencil-box-outline mr-2"></i>Edit
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-danger mr-2" wire:click="deleteConfirmation({{ $room->id }})">
                                                    <i class="mdi mdi-delete mr-2"></i>Delete
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-secondary" wire:click="openhistory({{ $room->id }})">
                                                    <i class="mdi mdi-history mr-2"></i></i>Guest History
                                                </a>
                                            </div>
                                        
                                        </td> --}}
                                    </tr>                 
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="6">No room data is found.</td>
                                    </tr>
                                @endif
                              </tbody>
                        </table>
                        @if($search && strlen($search) > 2)
                            {{-- leave it empty --}}
                        @else
                            <div class="d-flex justify-content-end mt-3">
                                {{ $rooms->links('vendor.livewire.bootstrap') }}
                            </div>
                        @endif
                    </div>         
                </div>
              </div>
        </div>

        {{-- Create Room Modal Form --}}
        <div wire:ignore.self class="modal fade" id="createRoomModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Room</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
    
                        <form wire:submit.prevent="submit" class="forms-sample">
                            <div class="d-flex justify-content-between">
                                <div class="form-group w-100 mr-3">
                                    <label for="room_no">Room Number <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" max="1000" id="room_no" wire:model.live.debounce.500ms="room_no"
                                        placeholder="Room Number (Ex. 101)">
                                @error('room_no')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                @enderror
                                </div>
                                <div class="form-group w-100">
                                    <label for="extra_bed">Extra Bed</label>
                                    <div class="input-group">
                                        <button wire:click="decrement" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                            <input wire:model.live.debounce.500ms="extra_bed" class="form-control rounded-0 text-center" id="extra_bed" type="number"  min="0" autocomplete="off" placeholder="Extra Bed" readonly/>
                                        <button wire:click="increment" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                    </div>
                                    @error('extra_bed')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="roomtype_id">Select Room Type <span class="text-danger">*</span></label>
                                <select type="text" class="form-control" id="roomtype_id" wire:model.live="roomtype_id">
                                    <option value="0">Select Room Type</option>
                                    @foreach($roomtypes as $roomtype)
                                        <option value="{{$roomtype->id}}">{{$roomtype->roomtype}}</option>
                                    @endforeach
                                </select>
                            @error('roomtype_id')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                            </div>
                            <button type="submit" class="btn btn-primary"
                            {{ $errors->any() || is_null($room_no) || is_null($roomtype_id) ? 'disabled' : '' }}>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Room Modal Form --}}
        <div wire:ignore.self class="modal fade" id="editRoomModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Room</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
    
                        <form wire:submit.prevent="editRoomData">
                            <div class="d-flex justify-content-between">
                                <div class="form-group w-100 mr-3">
                                    <label for="room_no">Room Number <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" max="1000" id="room_no" wire:model.live.debounce.500ms="room_no"
                                        placeholder="Room Number (Ex. 101)">
                                @error('room_no')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                @enderror
                                </div>
                                <div class="form-group w-100">
                                    <label for="extra_bed">Extra Bed</label>
                                    <div class="input-group">
                                        <button wire:click="decrement" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                            <input wire:model.live.debounce.500ms="extra_bed" class="form-control rounded-0 text-center" id="extra_bed" type="number"  min="0" autocomplete="off" placeholder="Extra Bed" readonly/>
                                        <button wire:click="increment" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                    </div>
                                    @error('extra_bed')
                                        <span class="text-danger text-xs">{{$message}}</span> 
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="roomtype_id">Select Room Type</label>
                                <select type="text" class="form-control" id="roomtype_id" wire:model.live="roomtype_id">
                                    @foreach($roomtypes as $roomtype)
                                        <option value="{{$roomtype->id}}" @if($roomtype->id == $roomtype_id) selected @endif>{{$roomtype->roomtype}}</option>
                                    @endforeach
                                </select>
                            @error('roomtype_id')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                            </div>

                            <div class="form-group">
                                <label for="roomtype_id">Set Room Status</label>
                                <select type="text" class="form-control" id="roomtype_id" wire:model="room_status">
                                    @if($room_status != 'Occupied')   
                                        <option value="">Select Room Status</option>
                                        <option value="Vacant Ready" @if ($room_status === 'Vacant Ready') selected @endif>Vacant Ready</option>
                                    @endif
                                        <option value="Occupied" @if ($room_status === 'Occupied') selected @endif>Occupied</option>
                                    @if($room_status != 'Occupied')
                                        <option value="Reserved" @if ($room_status === 'Reserved') selected @endif>Reserved</option>
                                        <option value="Block" @if ($room_status === 'Block') selected @endif>Block</option>
                                        <option value="Vacant Clean" @if ($room_status === 'Vacant Clean') selected @endif>Vacant Clean</option>
                                        <option value="Vacant Dirty" @if ($room_status === 'Vacant Dirty') selected @endif>Vacant Dirty</option>
                                    @endif
                                </select>
                                {{-- <select type="text" class="form-control" id="roomtype_id" wire:model="room_status">
                                    <option value="">Select Room Status</option>
                                    <option value="Vacant Ready" @if ($room_status === 'Vacant Ready') selected @endif>Vacant Ready</option>
                                    <option value="Occupied" @if ($room_status === 'Occupied') selected @endif>Occupied</option>
                                    <option value="Reserved" @if ($room_status === 'Reserved') selected @endif>Reserved</option>
                                    <option value="Block" @if ($room_status === 'Block') selected @endif>Block</option>
                                    <option value="Vacant Clean" @if ($room_status === 'Vacant Clean') selected @endif>Vacant Clean</option>
                                    <option value="Vacant Dirty" @if ($room_status === 'Vacant Dirty') selected @endif>Vacant Dirty</option>
                                </select> --}}
                            @error('room_status')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                            </div>
                            <button type="submit" class="btn btn-primary"
                            {{ $errors->any() ? 'disabled' : '' }}>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div wire:ignore.self class="modal fade" id="deleteRoomModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Notification</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to remove this room?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteData()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
      </div>

      {{-- Prevent delete modal --}}
      <div wire:ignore.self class="modal fade" id="preventModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog border-light modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #191c24">
                    <h5 class="modal-title" id="preventModalLabel">Notification</h5>
                    <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" wire:click="cancel()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" style="background-color: #191c24">
                    <div class="container">
                        <p style="font-size: 1rem;">
                            Apologies, but the room is currently reserved or in use by a guest.
                        </p>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #191c24">
                    <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

      {{-- Guest Room History --}}
      @livewire('room-history')

       {{-- Guest Room History --}}
       @livewire('guest-folio')

</div>

@section('scripts')
<script>
    window.addEventListener('close-room-modal', event =>{
        $('#createRoomModal').modal('hide');
        $('#editRoomModal').modal('hide');
        $('#deleteRoomModal').modal('hide');
        $('#viewRoomHistory').modal('hide');
        $('#preventModal').modal('hide');
    });

    window.addEventListener('show-guest-folio-modal', event =>{
        $('#guestFolio').modal('show');
    });

    window.addEventListener('close-guest-folio-modal', event =>{
        $('#guestFolio').modal('hide');
    });

    window.addEventListener('show-create-room-modal', event =>{
        $('#createRoomModal').modal('show');
    });

    window.addEventListener('show-edit-room-modal', event =>{
        $('#editRoomModal').modal('show');
    });

    window.addEventListener('show-delete-room-modal', event =>{
        $('#deleteRoomModal').modal('show');
    });

    window.addEventListener('show-room-history-modal', event =>{
        $('#viewRoomHistory').modal('show');
    });

    window.addEventListener('show-prevent-modal', event =>{
        $('#preventModal').modal('show');
    });
    
</script>
@endsection


