<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\RoomBooking;
use App\Models\BookingChildAge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_id',
        'check_in',
        'check_out',
        'room',
        'adult',
        'children',
        'extend_hours',
        'extra_bed',
        'extend_days',
        'total_amount',
        'remaining_amount',
        'check_in_status',
        'check_out_status',
        'payment_id',
        'cancel_status',
        'folio_no',
        'room_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guest(){
        return $this->belongsTo(Guest::class);
    }

    public function roomBooking()
    {
        return $this->hasMany(RoomBooking::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}
