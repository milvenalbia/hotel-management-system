<?php

namespace App\Models;

use App\Models\Guest;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTransaction extends Model
{
    use HasFactory;

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_transaction_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    protected $fillable = [
        'user_id',
        'guest_id',
        'discount',
        'total_amount',
        'cash_amount',
        'payment_method',
        'change',
    ];
}
