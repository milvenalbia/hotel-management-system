<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card justify-content-between">
          <div class="card bg-dark">
            <div class="card-body py-0 px-0 px-sm-3">
              <div class="row align-items-center">
                <div class="col-lg-2 col-sm-2 col-xl-2">
                    <img src={{asset("admin/assets/images/dashboard/Group126@2x.png")}} class="img-fluid" alt="">
                </div>
                <div class="col-lg-4 col-sm-4 col-xl-4 pr-2">
                  <h3 class="mb-1 mb-sm-0">Manage Product List</h3>
                </div>
                <div class="col-lg-6 col-sm-6 col-xl-6 pl-0">
                    <input wire:model.live.debounce.200ms="search" class="form-control text-dark bg-white border-0 shadow rounded-pill" placeholder="Search product...">
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

            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="mt-2 text-dark">Product List</h4>
                        <div class="d-flex justify-content-end col-10">
                            <select class="form-control text-white bg-dark" wire:model.live.debounce.200ms="byCategory" style="width: 20%;height: 50px;">
                                <option value="">Category</option>
                                <option value="Appetizer">Appetizer</option>
                                <option value="Salad">Salad</option>
                                <option value="Main Course">Main Course</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Beverage">Beverage</option>  
                            </select>
                            <button class="btn btn-primary rounded ml-2 pt-2" wire:click="addNewProduct"><i class="mdi mdi-plus-circle mr-1"></i> Add New Product</a>
                      </div>
                    </div>
                  <div class="table-responsive mt-4">
                    <table class="table text-dark">
                      <thead>
                        <tr>
                          <th> # </th>
                          <th> Product Name </th>
                          <th> Price </th>
                          <th> No. of Sold Item</th>
                          <th> Status </th>
                          <th> Created </th>
                          <th> Updated </th>
                          <th> Action </th>
                        </tr>
                      </thead>
                      <tbody>
                        @if ($products->count() > 0)
                            @foreach ($products as $d => $product)
                            <tr>
                                <td>{{++$d}}</td>
                                <td><img src="{{ Storage::url($product->image) }}" alt="Room Image" class="img-fluid mr-3">{{ $product->product_name }}
                                </td>
                                <td>&#8369;{{ number_format($product->product_price, 2, '.', ',') }}</td>
                                <td>
                                    @if($product->sold_item == null)
                                        N/A
                                    @else
                                        {{ $product->sold_item }}
                                    @endif
                                    
                                </td>
                                <td>{{ $product->status }}</td>
                                <td>{{$product->created_at->format('M d, Y')}}</td>
                                <td>{{$product->updated_at->diffForHumans()}}</td>
                                <td>
                                    <div class="d-flex">
                                        
                                        <a href="#" class="btn btn-md btn-primary mr-2" wire:click="editProduct({{ $product->id }})">
                                            <i class="mdi mdi-pencil-box-outline mr-1"></i>Edit
                                        </a>
                                        <a href="#" class="btn btn-md btn-danger mr-2" wire:click="deleteConfirmation({{ $product->id }})">
                                            <i class="mdi mdi-delete mr-1"></i>Remove
                                        </a>
                                    </div>
                                </td>
                            </tr>                 
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="8">No product available.</td>
                            </tr>
                        @endif
                      </tbody>
                    </table>
                    @if ($search && strlen($search) > 2)
                        {{-- leave it empty --}}
                    @else
                        <div class="d-flex justify-content-end">
                            {{ $products->links() }}
                        </div>
                    @endif
                  </div>
                </div>
              </div>

        {{-- Add Product modal End --}}

              <div wire:ignore.self class="modal fade" id="addProductModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #191c24">
                            <h5 class="modal-title">Add Product</h5>
                            <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="background-color: #191c24">
        
                            <form wire:submit.prevent="submit" class="forms-sample">
                                <div class="form-group">
                                    <label for="product_name">Product Name <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="product_name" type="text" class="form-control" id="product_name" placeholder="Enter Product Name">
                                    @error('product_name') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select type="text" class="form-control" id="category" wire:model.live.debounce.500ms="category">
                                        <option value="">Select Product Type</option>
                                        <option value="Appetizer">Appetizer</option>
                                        <option value="Salad">Salad</option>
                                        <option value="Main Course">Main Course</option>
                                        <option value="Dessert">Dessert</option>
                                        <option value="Beverage">Beverage</option>  
                                    </select>
                                    @error('category') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="product_price">Price <span class="text-danger">*</span></label>
                                    <input wire:model.live.debounce.500ms="product_price" type="number" class="form-control" id="product_price" placeholder="Enter Product Price">
                                    @error('product_price') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Image upload <span class="text-danger">*</span></label>
                                    <input wire:model="image" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                        </span>
                                    </div>
                                    @error('image') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                    @if ($image)
                                    <h4>Image Preview:</h4>
                                    <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-md">
                                    @endif
                                </div>
                                <button type="submit" id="submit" class="btn btn-primary"
                                {{ $errors->any() || is_null($product_name) || is_null($category) || is_null($product_price) || is_null($image) ? 'disabled' : '' }}>Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        {{-- Add Product modal End --}}

        {{-- Edit Modal --}}

        <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #191c24">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close" wire:click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background-color: #191c24">
    
                        <form wire:submit.prevent="editProductData">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input wire:model.live.debounce.500ms="product_name" type="text" class="form-control" id="product_name" placeholder="Enter Product Name">
                                @error('product_name') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select type="text" class="form-control" id="category" wire:model.live="category">
                                    <option value="">Select Product Type</option>
                                    <option value="Appetizer" @if ($category === 'Appetizer') selected @endif>Appetizer</option>
                                    <option value="Salad" @if ($category === 'Salad') selected @endif>Salad</option>
                                    <option value="Main Course" @if ($category === 'Main Course') selected @endif>Main Course</option>
                                    <option value="Dessert" @if ($category === 'Dessert') selected @endif>Dessert</option>
                                    <option value="Beverage" @if ($category === 'Beverage') selected @endif>Beverage</option>
                                </select>
                                @error('category') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="product_price">Price</label>
                                <input wire:model.live.debounce.500ms="product_price" type="number" class="form-control" id="product_price" placeholder="Enter Room product_Price">
                                @error('product_price') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="status">Product Status</label>
                                <select type="text" class="form-control" id="status" wire:model.live="status">
                                    <option value="">Select Product Status</option>
                                    <option value="Available" @if ($status === 'Available') selected @endif>Available</option>
                                    <option value="Not Available" @if ($status === 'Not Available') selected @endif>Not Available</option>
                                </select>
                                @error('status') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Image upload</label>
                                <input wire:model="image" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default">
                                <div class="input-group col-xs-12 mb-2">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                                @error('image') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                                @if($oldImage)
                                <h4>Old Image</h4>
                                <img src="{{Storage::url($oldImage)}}" alt="" class="img-md">
                                @endif
                                    @if ($image)
                                    <h4>New Image</h4>
                                    <img src="{{ $image->temporaryUrl() }}" class="img-md">
                                @endif
                            </div>
                            <button type="submit" id="edit" class="btn btn-primary"
                            {{ $errors->any() ? 'disabled' : '' }}>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- End of Edit Modal --}}

              {{-- Delete Confirmation Modal --}}
        <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to delete this product?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteData()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- End of Delete --}}
</div>
@section('scripts')
<script>
    window.addEventListener('close-product-modal', event =>{
        $('#editModal').modal('hide');
        $('#deleteModal').modal('hide');
        $('#addProductModal').modal('hide');
    });

    window.addEventListener('show-product-edit-modal', event =>{
        $('#editModal').modal('show');
    });

    window.addEventListener('show-delete-confirmation-modal', event =>{
        $('#deleteModal').modal('show');
    });

    window.addEventListener('show-add-product-modal', event =>{
        $('#addProductModal').modal('show');
    });
</script>

<script>
    document.addEventListener('livewire:init', function () {
        const productNameInput = document.getElementById('product_name');

        productNameInput.addEventListener('input', function () {
            // Remove non-alphabetical characters using a regular expression
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

    });
</script>

@endsection

