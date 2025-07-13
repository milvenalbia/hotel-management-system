<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card bg-dark">
            <div class="card-body py-0 px-0 px-sm-3">
              <div class="row align-items-center">
                <div class="col-4 col-sm-3 col-xl-2">
                  <img src="admin/assets/images/dashboard/Group126@2x.png" class="gradient-corona-img img-fluid" alt="">
                </div>
                <div class="col-5 col-sm-7 col-xl-8 p-0">
                  <h3 class="mb-1 mb-sm-0">Manage Employee</h3>
                </div>
                <div class="col-3 col-sm-2 col-xl-2 pl-0 text-center">
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
            <div class="card text-dark" style="background-color: #e6e9ed;">
                <div class="card-body">
                  <h4 class="card-title mb-4 text-dark text-uppercase">Add Employee Form</h4>
                  <form wire:submit.prevent="submit" class="forms-sample">
                    @if ($image)
                        <div class="container-fluid mb-2 text-center">
                            <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-profile">
                        </div>
                    @else
                        <div class="container-fluid mb-2 text-center">
                            <img src="{{asset("admin/assets/images/profile.jpg")}}" alt="Image Preview" class="img-profile">
                        </div>
                    @endif

                    {{-- <div class="container-fluid mb-2 text-center">
                        <button class="file-upload-browse" type="button">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-profile">
                            @else
                                <img src="{{asset("admin/assets/images/profile.jpg")}}" alt="Image Preview" class="img-profile">
                            @endif
                        </button>  
                    </div> --}}

                    <div class="form-group">
                        <input wire:model="image" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default bg-dark">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info bg-dark" disabled placeholder="Upload Profile picture">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                        @error('image') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input wire:model.live.debounce.500ms="name" type="text" class="form-control bg-dark" id="name" placeholder="Enter Full Name">
                        @error('name') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select wire:model.live.debounce.500ms="role" class="form-control bg-dark" id="role">
                            <option value="Employee">Employee</option>
                            <option value="Admin">Admin</option>
                        </select>
                        @error('role') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="hotel">Hotel Name <span class="text-danger">*</span></label>
                        <input wire:model.live.debounce.500ms="hotel" type="text" class="form-control bg-dark" id="hotel" placeholder="Enter Hotel Name">
                        @error('hotel') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input wire:model.live.debounce.500ms="username" type="text" class="form-control bg-dark" id="username" placeholder="Enter Username">
                        @error('username') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control bg-dark" id="password" wire:model.live.debounce.500ms="password"
                            placeholder="Enter Password">
                    @error('password')
                    <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    </div>
                        @if ($logo)
                            <div class="container-fluid mb-2 text-center">
                                <img src="{{ $logo->temporaryUrl() }}" alt="Image Preview" class="img-md">
                            </div>
                        @else
                            <div class="container-fluid mb-2 text-center">
                                <img src="{{asset("admin/assets/images/img-photo.png")}}" alt="Image Preview" class="img-md border border-dark">
                            </div>
                        @endif
                    <div class="form-group">
                        {{-- <label>Hotel Logo <span class="text-danger">*</span></label> --}}
                        <input wire:model="logo" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default bg-dark">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info bg-dark" disabled placeholder="Upload Hotel Logo">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload Logo</button>
                            </span>
                        </div>
                        @error('logo') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary"
                    {{ $errors->any() || is_null($name) || is_null($role) || is_null($hotel) || is_null($username) || is_null($password) || is_null($logo) || is_null($image) ? 'disabled' : '' }}>
                    Submit</button>
                </form>
                </div>
              </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title text-dark">User List</h4>
                            <input wire:model.live.debounce.200ms="search" class="form-control w-50 bg-dark" style="margin-top: -10px" placeholder="Search users...">
                    </div>
                  <div class="table-responsive">
                    <table class="table text-dark">
                      <thead >
                        <tr style="color: rgb(255, 251, 251);">
                          <th> # </th>
                          <th> Full Name </th>
                          <th> Role </th>
                          <th> Hotel Name</th>
                          <th> Username </th>
                          <th> Actions </th>
                        </tr>
                      </thead>
                      <tbody>
                        @if ($users->count() > 0)
                            @foreach ($users as $d => $user)
                            <tr>
                                <td>{{++$d}}</td>
                                <td><img src="{{ Storage::url($user->profile_image) }}" alt="Image" class="img-fluid mr-3">{{ $user->name }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->hotel_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-md btn-primary mr-2" wire:click="editUser({{ $user->id }})">
                                            <i class="mdi mdi-pencil-box-outline mr-2"></i>Edit
                                        </a>
                                        <a href="#" class="btn btn-md btn-danger" wire:click="deleteConfirmation({{ $user->id }})">
                                            <i class="mdi mdi-delete mr-2"></i>Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>          
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="6">No user's data is found.</td>
                            </tr>
                        @endif
                      </tbody>
                    </table>
                    @if ($search && strlen($search) > 2)
                        {{-- leave this empty --}}
                    @else
                    <div class="d-flex justify-content-end">
                            {{ $users->links() }}
                        </div>
                    @endif
                  </div>
                </div>
              </div>
        </div>

        {{-- Edit Modal Form --}}
        <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #191c24">
                        <h5 class="modal-title">Edit User Information</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background-color: #191c24">
    
                        <form wire:submit.prevent="editUserData">
                            <form wire:submit.prevent="submit" class="forms-sample">
                                <div class="d-flex justify-content-center">
                                    @if($oldImage)
                                        <div class="w-100 text-center">
                                            <h4 class="mt-2">Profile</h4>
                                            <img src="{{Storage::url($oldImage)}}" alt="" class="img-profile">
                                        </div>
                                    @endif
                                    @if ($editimage)
                                        <div class="w-100 text-center">
                                            <h4 class="mt-2">New Profile</h4>
                                            <img src="{{ $editimage->temporaryUrl() }}" class="img-profile">
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Profile Picture <span class="text-danger">*</span></label>
                                    <input wire:model="editimage" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Profile Picture">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                        </span>
                                    </div>
                                    @error('editimage') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="editname">Full Name <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="editname" type="text" class="form-control" id="editname">
                                    @error('editname') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="editrole">Role <span class="text-danger">*</span></label>
                                    <select wire:model.live.debounce.500ms="editrole" class="form-control" id="editrole">
                                        <option value="Employee">Employee</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                    @error('editrole') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="edithotel">Hotel Name <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="edithotel" type="text" class="form-control" id="edithotel">
                                    @error('edithotel') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="editusername">Username <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="editusername" type="text" class="form-control" id="editusername">
                                    @error('editusername') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="editpassword">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="editpassword" wire:model.live.debounce.500ms="editpassword" placeholder="Enter Password">
                                @error('editpassword')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-center">
                                @if($oldlogo)
                                    <div class="w-100">
                                        <h4 class="mt-2">Logo</h4>
                                        <img src="{{Storage::url($oldlogo)}}" alt="" class="img-md">
                                    </div>
                                @endif
                                @if ($editlogo)
                                    <div class="w-100">
                                        <h4 class="mt-2">New Logo</h4>
                                        <img src="{{ $editlogo->temporaryUrl() }}" class="img-md">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Hotel Logo <span class="text-danger">*</span></label>
                                <input wire:model="editlogo" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload Logo</button>
                                    </span>
                                </div>
                                @error('editlogo') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary"
                            {{ $errors->any() ? 'disabled' : '' }}>Submit</button>
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
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to delete the account of {{$user_name}}?
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
                                Apologies, the account deletion request cannot be processed.
                                The system requires at least one administrator to be present.
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

    window.addEventListener('show-prevent-delete-modal', event =>{
        $('#preventModal').modal('show');
    });
</script>
<script>
    document.addEventListener('livewire:init', function () {
        const nameInput = document.getElementById('name');
        const editnameInput = document.getElementById('editname');

        nameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        editnameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

    });
</script>
@endsection

