<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ManageProduct extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $status;
    public $product_name;
    public $product_stock;
    public $product_price;
    public $image;
    public $category;
    public $stock_in;
    public $stock_out;
    public $fetchProduct;

    #[url]
    public $search = '';

    #[url]
    public $byCategory;

    public function editProduct($id)
    {

        $this->fetchProduct = Product::where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->where('remove_status', false)
        ->first();

        $this->product_edit_id = $this->fetchProduct->id;
        $this->status = $this->fetchProduct->status;
        $this->product_name = $this->fetchProduct->product_name;
        $this->product_price = $this->fetchProduct->product_price;
        $this->category = $this->fetchProduct->category;
        $this->oldImage = $this->fetchProduct->image;

        $this->dispatch('show-product-edit-modal');
    }

    protected $rules = [
        'product_name' => 'required',
        'product_price' => 'required|numeric|min:1',
        'image' => 'required|image|max:1024',
        'category' => 'required',
    ];

    public function updated($propertyName)
    {
        if(!empty($this->fetchProduct)){
            $this->validateOnly($propertyName,[
                'product_name' => 'required',
                'product_price' => 'required|numeric|min:1',
                'image' => 'required|image|max:1024',
                'category' => 'required',
            ]);
        }else{
            $this->validateOnly($propertyName); 
        }
        
    }

    public function submit()
    {
        $validatedData = $this->validate();
        
        if($this->image){
            $validatedData['image'] = $this->image->store('public/photos');
        }

        Product::create([
            'user_id' => auth()->user()->id,
            'product_name' => $this->product_name,
            'product_price' => $this->product_price,
            'image' => $validatedData['image'],
            'category' => $this->category,
        ]);

        $this->reset();

        session()->flash('success', 'Product Created Successfully');

        $this->dispatch('close-product-modal');
    }
    
    public function render()
    {

        $query = Product::query()
        ->where('user_id', auth()->user()->id)
        ->where('remove_status', false);

        if ($this->byCategory) {
            $query->where('category', $this->byCategory);
        }

        if($this->search && strlen($this->search) > 2){
            $products = $query->where(function($query) {
                $query->where('product_name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_price', 'like', '%' . $this->search . '%');
            })
            ->get();
        }else {
            $products = $query->paginate(10);
        }


        return view('livewire.POS.manage-product',compact('products'));
    }

    public function addNewProduct(){

        $this->dispatch('show-add-product-modal');
    }


    public $product_edit_id;
    public $oldImage;
    
    public function editProductData()
    {

        $update_products = Product::findOrFail($this->product_edit_id);
        
        //on form submit validation
        $this->validate([
            'product_name' => 'required',
            'product_price' => 'required|numeric',
            'image' => 'max:1024', 
            'category' => 'required',
        ]);

        // if(!empty($this->stock_in) && empty($this->stock_out)){
        //     $add_stock = $this->stock_in + $update_products->product_stock;

        //     $photo = $update_products->image;
        //     if($this->image)
        //     {
        //         Storage::delete($update_products->image);
        //         $photo = $this->image->store('public/photos');
        //     }else{
        //         $photo = $update_products->image;
        //     }
 
        //     $update_products->update([
        //         'user_id' => auth()->user()->id,
        //         'product_name' => $this->product_name,
        //         'product_stock' => $add_stock,
        //         'product_price' => $this->product_price,
        //         'image' => $photo,
        //         'category' => $this->category,
        //     ]);

        //     $this->product_edit_id='';

        //     session()->flash('success', 'Product has been updated successfully');

        //     $this->reset();

        //     $this->dispatch('close-product-modal');

        // }elseif(!empty($this->stock_out) && empty($this->stock_in)){

        //     if($this->stock_out > $update_products->product_stock){

        //         $this->addError('stock_out', 'Stock quantity cannot be greater than ' . $update_products->product_stock);

        //     }else{

        //     $deduct_stock = $update_products->product_stock - $this->stock_out;
        //     $photo = $update_products->image;
        //     if($this->image)
        //     {
        //         Storage::delete($update_products->image);
        //         $photo = $this->image->store('public/photos');
        //     }else{
        //         $photo = $update_products->image;
        //     }
 
        //     $update_products->update([
        //         'product_name' => $this->product_name,
        //         'product_stock' => $deduct_stock,
        //         'product_price' => $this->product_price,
        //         'image' => $photo,
        //         'category' => $this->category,
        //     ]);

        //     $this->product_edit_id='';

        //     session()->flash('success', 'Product has been updated successfully');

        //     $this->reset();

        //     $this->dispatch('close-product-modal');
        // }

        // }elseif(!empty($this->stock_in) && !empty($this->stock_out)){
        //     if($this->stock_out > $update_products->product_stock){

        //         $this->addError('stock_out', 'Stock quantity cannot be greater than ' . $update_products->product_stock);

        //     }else{
                
        //     $calculate_stock = $this->stock_in + $update_products->product_stock - $this->stock_out;

        //     $photo = $update_products->image;
        //     if($this->image)
        //     {
        //         Storage::delete($update_products->image);
        //         $photo = $this->image->store('public/photos');
        //     }else{
        //         $photo = $update_products->image;
        //     }
 
        //     $update_products->update([
        //         'product_name' => $this->product_name,
        //         'product_stock' => $calculate_stock,
        //         'product_price' => $this->product_price,
        //         'image' => $photo,
        //         'category' => $this->category,
        //     ]);

        //     $this->product_edit_id='';

        //     session()->flash('success', 'Product has been updated successfully');

        //     $this->reset();

        //     $this->dispatch('close-product-modal');
        //     }
            
        // }else{

            $photo = $update_products->image;
            if($this->image)
            {
                Storage::delete($update_products->image);
                $photo = $this->image->store('public/photos');
            }else{
                $photo = $update_products->image;
            }
 
            $update_products->update([
                'product_name' => $this->product_name,
                'product_price' => $this->product_price,
                'image' => $photo,
                'category' => $this->category,
                'status' => $this->status,
            ]);

            $this->product_edit_id='';

            session()->flash('success', 'Product has been updated successfully');

            $this->reset();

            $this->dispatch('close-product-modal');
        
    }

    public function close()
    {

        $this->reset();

        $this->resetValidation();

        $this->dispatch('close-product-modal');
    }


    // Delete Confirmation Modal

    public $product_id;

    public function deleteConfirmation($id)
    {
        $this->product_id = $id;

        $this->dispatch('show-delete-confirmation-modal');
    }

    public function deleteData()
    {
 
        $products = Product::where('user_id', auth()->user()->id)
        ->where('id', $this->product_id)->first();

        $products->update([
            'remove_status' => true,
        ]);

        session()->flash('success', 'product has been removed successfully');

        $this->dispatch('close-product-modal');

        $this->product_id = '';
    }

    public function cancel()
    {
        $this->product_id = '';
    }
}
