<?php

namespace App\Livewire;

use App\Models\Guest;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderItem;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use App\Models\OrderTransaction;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ManageOrder extends Component
{
    use WithPagination;

    public $guest_name;
    public function mount($bookId)
    {
        $booking = BookingTransaction::where('user_id', auth()->user()->id)
        ->where('id', $bookId)->first();

        $this->guest_id = $booking->guest_id;

        $this->guest_name = $booking->guest->firstname . ' ' . $booking->guest->lastname;
    }
    

    #[Url] 
    public $search= '';

    #[url]
    public $byCategory;

    public function render()
    {

        $query = Product::query()
        ->where('user_id', auth()->user()->id)
        ->where('remove_status', false);

        if ($this->byCategory) {
            $query->where('category', $this->byCategory);
        }

        if($this->search && strlen($this->search) > 2) {
            $products = $query->where(function($query) {
                $query->where('product_name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_price', 'like', '%' . $this->search . '%');
            })
            ->get();
        }else {
            $products = $query->paginate(9);
        }

        
        // $productLow = Product::where('product_stock', '<', 20)
        // ->where('remove_status', false)
        // ->get();

        // // $this->showWarningModal();


        return view('livewire.POS.manage-order', compact('products'));
    }

    public $cart = [];
    public $quantity = 1;
    public $disabledButtons = [];
    public $tax;
    public $items;

    public function addToCart($productId)
    {

        $this->disabledButtons[$productId] = true;
        // Find the product by its ID and add it to the cart array
        $product = Product::where('user_id', auth()->user()->id)
        ->where('id', $productId)
        ->where('remove_status', false)
        ->first();

                $this->cart[] = [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'quantity' => 1,
                    'price' => $product->product_price,
                    'total_price' => $product->product_price,
                ];
    
                // Recalculate cart totals
                $this->calculateCartTotal();

                if(!empty($this->discount) && $this->discount > 0){
                    $this->updatedDiscount();
                }else{
                    $this->calculateCartTotal();
                }
                
                $this->calculateTotalItemPrice();
                $this->items += 1;

                $this->updatedDiscount();
}

    public function increment($index)
{

    $product = Product::find($this->cart[$index]['id']);
    if (isset($this->cart[$index])) {
        $this->cart[$index]['quantity']++;
        // Recalculate the total price for the updated product
        $this->cart[$index]['total_price'] = $this->cart[$index]['price'] * $this->cart[$index]['quantity'];
        
        $this->calculateCartTotal();

        if(!empty($this->discount) && $this->discount > 0){
            $this->updatedDiscount();
        }else{
            $this->calculateCartTotal();
        }

        if(empty($this->cart)){
            $this->discount = 0 ;
        }

        $this->calculateTotalItemPrice();

        $this->items += 1;

    }
}

public function decrement($index)
{
    if (isset($this->cart[$index]) && $this->cart[$index]['quantity'] > 1) {
        $this->cart[$index]['quantity']--;
        // Recalculate the total price for the updated product
        $this->cart[$index]['total_price'] = $this->cart[$index]['price'] * $this->cart[$index]['quantity'];

        $this->resetValidation('quantity');

        $this->calculateCartTotal();

        if(!empty($this->discount) && $this->discount > 0){
            $this->updatedDiscount();
        }else{
            $this->calculateCartTotal();
        }

        $this->calculateTotalItemPrice();
        
        $this->items -= 1;
    }
}

    public function removeItem($index)
    {

        if (isset($this->cart[$index])) {
            $productId = $this->cart[$index]['id']; // Get the product ID
            $removedQuantity = $this->cart[$index]['quantity'];

            // Remove the item from the cart using the provided index
            unset($this->cart[$index]);
            // Reindex the cart array
            $this->cart = array_values($this->cart);
    
            // Enable the "Add to Cart" button for the removed product
            $this->disabledButtons[$productId] = false;
           
            $this->calculateCartTotal();

            $this->calculateTotalItemPrice();

            $this->items -= $removedQuantity;

            $this->resetValidation('quantity');       
                
                if(empty($this->cart)){
                    $this->discount = 0;
                }

        }
    }

    public $total_item_price;
    public $cartTotal;
    
    public function calculateCartTotal()
    {
        $total = 0;
    
        foreach ($this->cart as $item) {
            $total += $item['total_price'];
        }
    
        $this->cartTotal = $total;

    }

    public function calculateTotalItemPrice()
    {
        $totals = 0;
    
        foreach ($this->cart as $item) {
            $totals += $item['total_price'];
        }
    
        $this->total_item_price = $totals;

        if(!empty($this->cash_amount)){
            $this->updatedCashAmount();
        }
    }

    public function updatedDiscount(){

        $this->calculateCartTotal();

        if($this->discount > 0){
            $this->cartTotal = ($this->cartTotal - ($this->cartTotal * $this->discount / 100));
        }else{
            $this->calculateCartTotal();
        }
    }


    // public function updatedCashAmount()
    // {
    //     if(!empty($this->cash_amount) || !empty($this->cartTotal)){

    //         if($this->cash_amount >= $this->cartTotal){
    //             $this->change = $this->cash_amount - $this->cartTotal;

    //             $this->change = number_format($this->change, 2, '.', ',');
    //         }else{
    //             $this->addError('cash_amount', 'Cash amount cannot be less than ' . $this->cartTotal);

    //             $this->change = '';
    //         }
        
    //     }
    //     else{
    //         $this->change = '';
    //     }
    // }

    // public function updatedGuestId(){
    //     dd($this->guest_id);
    // }

    #[Rule('required')]
    public $cash_amount;

    #[Rule('nullable')]
    public $discount = 0;

    public $guest_id;
    public $change;

    // public $checked_button = false;
    // public $showButton = true;

    // public function updatedCheckedButton(){

    //     if($this->checked_button == true){
    //         $this->showButton = false;
    //     }else{
    //         $this->showButton = true;
    //     }

    //     if(!empty($this->cash_amount)){
    //         $this->resetValidation('cash_amount');
    //     }
        
    // }


    public function submitOrder(){

        // if($this->checked_button == true){
        //     $this->validate([
        //         'cash_amount' => 'nullable'
        //     ]);
        // }else{
        //     $this->validate();
        // }

        if(empty($this->discount)){
            $discount = 0;
        }else{
            $discount = $this->discount;
        }

        $user = Auth::user();

        if ($user) {
            // if($this->checked_button){
                $user_id = $user->id;
               
                $order = new OrderTransaction();
                $order->user_id = $user_id;
                $order->guest_id = $this->guest_id;
                $order->discount = $discount;
                $order->total_amount = $this->cartTotal;
                $order->cash_amount = 0;
                $order->payment_method = 'none';
                $order->change = 0;
                $order->save();

                $orders = OrderTransaction::where('user_id', $user->id)
                ->where('guest_id', $this->guest_id)->first();

                $guest = BookingTransaction::where('user_id', $user->id)
                ->where('guest_id', $orders->guest_id)->first();

                if($guest && $guest->order_cost != null){
                    $guest->order_cost += $this->cartTotal;
                    $guest->save();
                }else{
                    $guest->order_cost = $this->cartTotal;
                    $guest->save();
                }

                foreach ($this->cart as $cartItem) {
                    $orderItem = new OrderItem([
                        'product_id' => $cartItem['id'],
                        'order_id' => $order->id,
                        'quantity' => $cartItem['quantity'],
                        'product_price' => $cartItem['price'],
                        'total_price' => $cartItem['total_price'],
                    ]);
                    $order->orderItems()->save($orderItem);
        
                    
                    $product = Product::find($cartItem['id']);
                    if ($product) {
                        $product->sold_item += $cartItem['quantity'];
                        $product->timestamps = false;
                        $product->save();
        
                        $product->timestamps = true;
                    }
                }

                $this->cart = [];
                $this->calculateCartTotal();
                $this->calculateTotalItemPrice();
                $this->items = 0;

                $this->reset();

                return redirect('/guest-record')->with('success', 'Order saved successfully!');
                
            // }else{
            //     $user_id = $user->id;
                
            //     $order = new OrderTransaction();
            //     $order->user_id = $user_id;
            //     $order->guest_id = $this->guest_id;
            //     $order->discount = $this->discount;
            //     $order->total_amount = $this->cartTotal;
            //     $order->cash_amount = $this->cash_amount;
            //     $order->change = $this->change;
            //     $order->save();

            //     $orders = OrderTransaction::where('user_id', $user->id)
            //     ->where('guest_id', $this->guest_id)->first();

            //     foreach ($this->cart as $cartItem) {
            //         $orderItem = new OrderItem([
            //             'product_id' => $cartItem['id'],
            //             'order_id' => $order->id,
            //             'quantity' => $cartItem['quantity'],
            //             'product_price' => $cartItem['price'],
            //             'total_price' => $cartItem['total_price'],
            //         ]);
            //         $order->orderItems()->save($orderItem);
        
                    
            //         $product = Product::find($cartItem['id']);
            //         if ($product) {
            //             $product->product_stock -= $cartItem['quantity'];
            //             $product->timestamps = false;
            //             $product->save();
        
            //             $product->timestamps = true;
            //         }
            //     }

            //     $this->cart = [];
            //     $this->calculateCartTotal();
            //     $this->calculateTotalItemPrice();
            //     $this->items = 0;

            //     $this->reset();

            //     return redirect()->route('print-receipt', ['id' => $order->id]);
            // }  

    }
        
        
}

// public $lowProduct = [];
// public $turn_off = false;

// public function showWarningModal(){

//     if($this->turn_off == false){
//     $products = Product::where('user_id', auth()->user()->id)
//     ->where('product_stock', '<', 20)
//     ->where('remove_status', false)
//     ->get();

//     foreach($products as $product){
//         $this->lowProduct[] = ([
//             'product_name' => $product->product_name,
//             'stock' => $product->product_stock,
//         ]);
//     }

//     if($products->count() > 0){
//         $this->dispatch('show-warning-modal');
//     }
        
//     }   
// }

// public function offNotify(){
//     $this->turn_off = true;
// }

// public function goToInventory(){
//     return redirect()->route('product');
// }


}
