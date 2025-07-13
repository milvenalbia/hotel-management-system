<div>
    <div class="d-flex justify-content-end">
        <ol class="breadcrumb text-large">
            <li class="breadcrumb-item"><i class="mdi mdi-subdirectory-arrow-left mr-1 text-primary"></i><a href="/guest-record">Back To Guest List</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Guest Order</li>
          </ol>
        
    </div>
    <div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="background-color: #e6e9ed;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title text-dark">Product List</h4>
                    <div class="d-flex justify-content-end col-9">
                        <input wire:model.live.debounce.200ms="search" class="form-control w-100 mr-2 bg-dark text-light" style="margin-top: -10px" placeholder="Search product . . .">
                        <select class="form-control w-50 bg-dark text-light" wire:model.live.debounce.200ms="byCategory" style="margin-top: -10px;">
                            <option value="">Category</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Salad">Salad</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Beverage">Beverage</option>  
                        </select>
                    </div>
                </div>
                @if($products)
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                        <div class="card border-dark">
                                <h4 class="preview-subject text-center" id="overlay-text2">{{$product->product_name}}</h4>
                            <div class="card-body" style="padding: 18px">
                                <div>
                                    <img src="{{ Storage::url($product->image) }}" alt="Room Image" class="img-product-md rounded pr-3">
                                    <div id="overlay-img2"></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                @if ($product->status == 'Available')
                                    <h4 class="text-dark">{{$product->status}}</h4>
                                    <p class="text-small text-dark">Product Price: &#8369;{{number_format($product->product_price, 2, '.', ',')}}</p>
                                    @if(isset($disabledButtons[$product->id]) && $disabledButtons[$product->id])
                                        <button type="submit" class="btn btn-md w-100 btn-primary text-center disabled" style="font-size: 1rem;padding: 0.6rem;">Added to Order</button>
                                    @else
                                        <button type="submit" class="btn btn-md w-100 btn-primary text-center" wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled" style="font-size: 1rem;padding: 0.6rem;">Add Order</button>
                                    @endif
                                @else
                                    <h4 class="text-dark">{{$product->status}}</h4>

                                    <p class="text-small text-dark">Product Price: &#8369;{{number_format($product->product_price, 2, '.', ',')}}</p>

                                    @if(isset($disabledButtons[$product->id]) && $disabledButtons[$product->id])
                                        <button type="submit" class="btn btn-md btn-primary w-100 text-center disabled" style="font-size: 1rem;padding: 0.6rem;">Added to Order</button>
                                    @else
                                        <button type="submit" style="font-size: 1rem; opacity: 20%; padding: 0.6rem;" class="btn btn-md btn-primary w-100 text-center disabled" style="cursor: not-allowed;">
                                            Not Available
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                @endif
                @if($search && strlen($search) > 2)
                    {{-- leave this empty --}}
                @else
                    <div class="d-flex justify-content-end">
                        {{ $products->links() }}
                    </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Order Cart --}}
          <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="background-color: #e6e9ed;">
                <div class="card-body" id="card-body">
                    
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-3 text-dark">Orders</h4>
                        @if($items > 0)
                            <h5 class="text-dark">({{$items}})&nbsp;Items</h5>
                        @else
                            <h5 class="text-dark">Items</h5>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between ms-2">
                        <p class="text-medium text-dark">Product Name</p >
                        <p class="text-medium text-dark" style="margin-left: 18px;">Quantity</p >
                        <p class="text-medium text-dark" style="margin-left: 22px;">Price</p >
                        <p class="text-medium text-dark" style="margin-left: 10px;">Total Price</p >
                        <p class="text-medium text-dark" style="margin-left: 10px;">Remove</p >
                    </div>
                    <hr style="border-top: solid 1px #000000; margin-top: -15px;">
                    @if (count($cart) > 0)
                    @foreach ($cart as $index => $item)
                    <div class="row mt-2">
                        <div class="col-sm-2 col-lg-4">
                            <p class="text-medium text-dark">{{ $item['product_name'] }}</p>
                        </div>
                        <div class="col-sm-2 col-lg-3">
                            <p class="text-medium text-dark" style="margin-left: -20px;">
                                <button wire:click="decrement({{ $index }})" class="btn btn-danger" id="btn-xxs">-</button>
                                <span class="text-medium ml-2">{{ $item['quantity'] }}</span>
                                <button wire:click="increment({{ $index }})" class="btn btn-primary ml-2" id="btn-xxs">+</button>
                            </p>
                        </div>
                        <div class="col-sm-2 col-lg-2">
                            <p class="text-medium text-dark" style="margin-left: -35px;">&#8369;{{number_format($item['price'], 0, '.', ',')}}</p>
                        </div>
                        <div class="col-sm-2 col-lg-2">
                            <p class="text-medium text-dark" style="margin-left: -50px;">&#8369;{{number_format($item['total_price'], 0, '.', ',')}}</p>
                        </div>
                        <div class="col-sm-2 col-lg-1">
                            <p class="text-medium text-dark" style="margin-left: -35px;"><button wire:click="removeItem({{ $index }})" class="btn btn-danger" id="btn-xxs">x</button></p>
                        </div>
                    </div>
                    @endforeach
                @endif
                {{-- <table class="table">
                    <tbody>
                        <tr>
                            <td>Product</td>
                            <td>Quantity</td>
                            <td>Price</td>
                            <td>Total Price</td>
                            <td>Remove</td>
                        </tr>
                    @if (count($cart) > 0)
                        @foreach ($cart as $index => $item)
                            <tr>
                                <td>{{ $item['product_name'] }}</td>
                                <td>
                                    <p class="text-medium text-dark">
                                        <button wire:click="decrement({{ $index }})" class="btn btn-danger" id="btn-xxs">-</button>
                                        <span class="text-medium ml-2">{{ $item['quantity'] }}</span>
                                        <button wire:click="increment({{ $index }})" class="btn btn-primary ml-2" id="btn-xxs">+</button>
                                    </p>
                                </td>
                                <td>&#8369;{{number_format($item['price'], 0, '.', ',')}}</td>
                                <td>&#8369;{{number_format($item['total_price'], 0, '.', ',')}}</td>
                                <td>
                                    <p class="text-medium text-dark"><button wire:click="removeItem({{ $index }})" class="btn btn-danger" id="btn-xxs">x</button></p>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No Order</td>
                        </tr>
                    @endif
                    </tbody>
                </table> --}}
                <hr style="border-top: solid 1px #000000 ;margin-bottom: 15px">
                @if ($errors->has('quantity'))
                    <span class="text-danger" style="font-size: 12px">{{ $errors->first('quantity') }}</span>
                @endif
                    <div class="form-group">
                        <label for="guest_name" class="text-dark">Guest Name</label>
                        <input type="text" class="form-control" wire:model.live="guest_name" id="guest_name" readonly>
                        @error('guest_name') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                    </div>
                        <div class="form-group">
                            <label for="discount" class="text-dark">Discount (%)</label>
                            <input wire:model.live.debounce.500ms="discount" type="number" class="form-control" id="discount" placeholder="Enter Discount">
                            @error('discount') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                        </div>
                <hr style="border-top: solid 1px #000000;">
                <div class="d-flex justify-content-end">
                    <p class="text-small text-dark">
                        <span class="text-small text-dark mr-1">Total Price:</span>
                        <span class="text-small text-dark ml-3">{{number_format($total_item_price, 0, '.', ',')}}</span>
                    </p>
                </div>
                @if($discount > 0)
                <div class="d-flex justify-content-end">
                    <p class="text-small text-dark">Discount (%): <span class="text-small text-dark ml-3">{{$discount}}%</span></p>
                </div>
                @endif
                <div class="d-flex justify-content-between mt-3">
                    <p class="text-small font-weight-bold text-dark">Total Cost</p>
                    <p class="text-small font-weight-bold text-dark">&#8369;{{ number_format($cartTotal, 2, '.', ',') }}</p>
                </div>


                    @if($guest_id == null ||  $cart == null || $cartTotal < 1)
                        <button type="submit" wire:click="submitOrder" class="btn btn-primary w-100 rounded-pill" style="line-height: 2rem;" disabled><span style="font-size: 18px">Place Order</span>
                        </button>
                        @else
                        <button type="submit" wire:click="submitOrder" class="btn btn-primary w-100 rounded-pill" style="line-height: 2rem;" >
                            Place Order
                        </button>
                    @endif

                </div>
              </div>
        </div>
        {{-- End of Order Cart --}}

        {{-- <div wire:ignore.self class="modal fade" id="warningMessage" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog border-light" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #191c24">
                        <h5 class="modal-title" id="deleteModalLabel">Notification</h5>
                    </div>
                    <div class="modal-body" style="background-color: #191c24">
                            <h4 class="text-white mx-5 my-3">
                                <strong class="text-warning">Warning</strong>, Product is Low! Stock In Now!
                            </h4>
                            <ul class="mx-5">
                                @foreach($lowProduct as $product)
                                    <li>
                                        @if($product['stock'] < 10)
                                        <p>
                                            Product Name:<span class="ml-2">{{ $product['product_name'] }}</span><br>
                                            Stock:<span class="text-danger ml-2">{{ $product['stock'] }}</span>
                                            
                                        </p>
                                        @else
                                        <p>
                                            Product Name:<span class="ml-2">{{ $product['product_name'] }}</span><br>
                                            Stock:<span class="text-warning ml-2">{{ $product['stock'] }}</span> 
                                        </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal" wire:click="offNotify">Later</button>
                            <button type="submit" class="btn btn-primary" wire:click="goToInventory()">Ok</button>
                            </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <form wire:submit.prevent="paypal">


            <input type="number" class="form_control" wire:model.live="paypal_amount">

            <button class="btn btn-sm btn-primary">Paypal</button>
        </form> --}}

        @if(Session::has('success'))
        <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom show showAlert">
            <span class="fas fa-check-circle ml-2"></span>
            <span class="text-white text-sm ml-5">{{session('success')}}</span>
        </div>
    @endif
        
    </div>
</div>

@section('scripts')

<script>
    const discountInput = document.getElementById('discount');
    
    discountInput.addEventListener('input', function () {
        if (this.value < 0) {
            this.value = 0;
        } else if (this.value > 100) {
            this.value = 100;
        }
    });
</script>
<script>
    document.addEventListener('keydown', function(event) {
        // Check if the pressed key is the desired shortcut (for example, 'Ctrl + C' or 'Ctrl + Enter')
        if (event.ctrlKey && event.key === 'c') {
            // Simulate a click on the button with the specified ID
            document.getElementById('check_button').click();
        }
    });
</script>

{{-- <script>
    window.addEventListener('close-warning-modal', event =>{
        $('#warningModal').modal('hide');

    });

    window.addEventListener('show-warning-modal', event =>{
        $('#warningMessage').modal('show');
    });

</script> --}}
@endsection