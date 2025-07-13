<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card bg-dark">
            <div class="card-body py-0 px-0 px-sm-3">
              <div class="row align-items-center">
                <div class="col-4 col-sm-3 col-xl-2">
                  <img src={{asset("admin/assets/images/dashboard/Group126@2x.png")}} class="gradient-corona-img img-fluid" alt="">
                </div>
                <div class="col-5 col-sm-7 col-xl-8 p-0">
                  <h3 class="mb-1 mb-sm-0">Manage Roomtypes</h3>
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
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                  <h4 class="card-title mb-3 text-dark">Create Room Type</h4>
                  <form wire:submit.prevent="submit" class="forms-sample">
                    <div class="form-group">
                        <label for="roomtype" class="text-dark">Room Type <span class="text-danger">*</span></label>
                        <input wire:model.live.debounce.500ms="roomtype" type="text" class="form-control bg-dark" id="roomtype" placeholder="Enter Room Type">
                        @error('roomtype') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="form-group w-100 mr-3">
                            <label for="capacity" class="text-dark">Capacity <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button wire:click="decrement" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                    <input wire:model.live.debounce.500ms="capacity" type="number" class="form-control bg-dark text-center" id="capacity" >
                                <button wire:click="increment" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                            </div>
                            
                            @error('capacity') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group w-100">
                            <label for="price" class="text-dark">Price <span class="text-danger">*</span></label>
                                <input wire:model.live.debounce.500ms="price" max="100000" type="number" class="form-control bg-dark" id="price" placeholder="Enter Room Price">
                            @error('price') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                        </div>
                    </div>
                        @if ($image)
                            <div class="container-fluid mb-2 text-center">
                                <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-md border border-dark">
                            </div>
                        @else
                            <div class="container-fluid mb-2 text-center">
                                <img src="{{asset("admin/assets/images/img-photo.png")}}" alt="Image Preview" class="img-md border border-dark" >
                            </div>
                        @endif
                    <div class="form-group">
                        <label class="text-dark" >Image upload <span class="text-danger">*</span></label>
                        <input wire:model="image" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control bg-dark file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                        @error('image') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1" class="text-dark">Description <span class="text-danger">*</span></label>
                        <textarea wire:model.live.debounce.500ms="description" class="form-control bg-dark" id="exampleTextarea1" rows="3" placeholder="Enter Room Description"></textarea>
                        @error('description') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary"
                    {{ $errors->any() || is_null($roomtype) || is_null($capacity) || is_null($price) || is_null($image) || is_null($description) ? 'disabled' : '' }}>Submit</button>
                </form>
                </div>
              </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card" >
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title text-dark">Roomtype List</h4>
                            <input wire:model.live.debounce.200ms="search" class="form-control bg-dark text-white w-50" style="margin-top: -10px" placeholder="Search Room Type">
                    </div>
                  <div class="table-responsive">
                    <table class="table text-dark">
                      <thead >
                        <tr>
                          <th> # </th>
                          <th> Room Type </th>
                          <th> Capacity </th>
                          <th> Price </th>
                          <th> Actions </th>
                        </tr>
                      </thead>
                      <tbody>
                        @if ($roomtypes->count() > 0)
                            @foreach ($roomtypes as $d => $roomtype)
                            <tr>
                                <td>{{++$d}}</td>
                                <td><img src="{{ Storage::url($roomtype->image) }}" alt="Room Image" class="img-fluid mr-3">{{ $roomtype->roomtype }}</td>
                                <td>{{ $roomtype->capacity }}</td>
                                <td>&#8369;{{ $roomtype->formatted_price }}</td>
                                <td>
                                        <div class="d-flex">
                                            <a href="#" class="btn btn-md btn-primary mr-2" wire:click="editRoomtype({{ $roomtype->id }})">
                                                <i class="mdi mdi-pencil-box-outline mr-2"></i>Edit
                                            </a>
                                            <a href="#" class="btn btn-md btn-danger" wire:click="deleteConfirmation({{ $roomtype->id }})">
                                                <i class="mdi mdi-delete mr-2"></i>Remove
                                            </a>
                                        </div>
                                    
                                </td>
                            </tr>                 
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="6">No roomtypes found.</td>
                            </tr>
                        @endif
                      </tbody>
                    </table>

                    @if($search && strlen($search) > 2)
                        {{-- leave this empty --}}
                    @else
                        <div class="d-flex justify-content-end">
                            {{ $roomtypes->links() }}
                        </div>
                    @endif
                    
                  </div>
                </div>
              </div>
        </div>

        {{-- Edit Modal Form --}}
        <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Room Type</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
    
                        <form wire:submit.prevent="editRoomtypeData">
                            <div class="form-group">
                                <label for="roomtype">Room Type</label>
                                <input wire:model.live.debounce.500ms="editroomtype" type="text" class="form-control" id="editroomtype" placeholder="Enter Room Type">
                                @error('editroomtype') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <div class="form-group w-100 mr-3">
                                    <label for="capacity" class="text-light">Capacity <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <button wire:click="editDecrement" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-minus"></i></button>
                                            <input wire:model.live.debounce.500ms="editcapacity" type="number" class="form-control text-center" id="capacity" placeholder="Enter Room Capacity" readonly>
                                        <button wire:click="editIncrement" type="button" class="btn rounded-0" style="background: #2A3038;"><i class="mdi mdi-plus"></i></button>
                                    </div>
                                    
                                    @error('editcapacity') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group w-100">
                                    <label for="price" class="text-light">Price <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="editprice" type="number" max="100000" class="form-control" id="price" placeholder="Enter Room Price">
                                    @error('editprice') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Image upload</label>
                                <input wire:model="editimage" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                                @error('editimage') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror

                                <div class="d-flex justify-content-between">
                                    @if($oldImage)
                                        <div class="w-100">
                                            <h4 class="mt-2">Old Image</h4>
                                            <img src="{{Storage::url($oldImage)}}" alt="" class="img-md">
                                        </div>
                                    @endif
                                    @if ($editimage)
                                        <div class="w-100">
                                            <h4 class="mt-2">New Image</h4>
                                            <img src="{{ $editimage->temporaryUrl() }}" class="img-md">
                                        </div>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <label for="exampleTextarea1">Description</label>
                                <textarea wire:model.live.debounce.500ms="editdescription" class="form-control" id="exampleTextarea1" rows="3" placeholder="Enter Room Description"></textarea>
                                @error('editdescription') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" {{ $errors->any() ? 'disabled' : '' }}>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Notification</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to remove {{$deleted_roomtypes}}?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteData()">Delete</button>
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
                            Apologies, but one of the room assigned to {{$deleted_roomtypes}} is currently reserved or in use by a guest.
                        </p>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #191c24">
                    <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
        
      </div>

</div>

@section('scripts')
<script>
    window.addEventListener('close-modal', event =>{
        $('#editModal').modal('hide');
        $('#deleteModal').modal('hide');
        $('#preventModal').modal('hide');
    });

    window.addEventListener('show-edit-modal', event =>{
        $('#editModal').modal('show');
    });

    window.addEventListener('show-delete-confirmation-modal', event =>{
        $('#deleteModal').modal('show');
    });

    window.addEventListener('show-prevent-modal', event =>{
        $('#preventModal').modal('show');
    });
</script>
<script>
    document.addEventListener('livewire:init', function () {
        const roomtypeInput = document.getElementById('roomtype');
        const editroomtypeInput = document.getElementById('editroomtype');

        roomtypeInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        editroomtypeInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });
    });
</script>
@endsection

