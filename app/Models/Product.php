<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected $fillable = [
        'user_id',
        'product_name',
        'product_price',
        'image',
        'category',
        'remove_status',
        'sold_item',
        'status',
    ];
}
