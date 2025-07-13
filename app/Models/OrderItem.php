<?php

namespace App\Models;

use App\Models\Product;
use App\Models\OrderTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(OrderTransaction::class, 'order_transaction_id');
    }   
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $fillable = [
        'order_transaction_id	',
        'product_id',
        'quantity',
        'product_price',
        'total_price',
    ];
}
