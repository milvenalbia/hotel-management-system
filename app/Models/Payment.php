<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'extra_bed_amount',
        'extended_amount',
        'paid_amount',
        'discount',
        'invoice_no',
        'payment_method',
        'change'
    ];
}
