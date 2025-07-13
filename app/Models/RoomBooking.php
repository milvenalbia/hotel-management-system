<?php

namespace App\Models;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_transaction_id',
        'room_id',
        'check_in_status',
        'check_out_status',
        'check_in',
        'check_out',
        'cancel_status'
    ];

    public function booking(){
        return $this->belongsTo(BookingTransaction::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}
